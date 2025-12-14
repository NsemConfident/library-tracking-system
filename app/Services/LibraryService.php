<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Fine;
use App\Models\Hold;
use App\Models\Loan;
use App\Models\User;
use App\Notifications\FineNotification;
use App\Notifications\HoldReadyNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LibraryService
{
    /**
     * Checkout a book copy to a user
     */
    public function checkout(Copy $copy, User $user, int $loanDays = 14): Loan
    {
        if (!$copy->isAvailable()) {
            throw new \Exception('This copy is not available for checkout.');
        }

        if ($user->activeLoans()->count() >= 10) {
            throw new \Exception('User has reached the maximum number of active loans.');
        }

        return DB::transaction(function () use ($copy, $user, $loanDays) {
            $loan = Loan::create([
                'copy_id' => $copy->id,
                'user_id' => $user->id,
                'checkout_date' => Carbon::today(),
                'due_date' => Carbon::today()->addDays($loanDays),
                'status' => 'active',
                'checked_out_by' => auth()->id(),
            ]);

            $copy->update(['status' => 'checked_out']);

            AuditLog::log('checkout', 'Loan', $loan->id, "Book checked out: {$copy->book->title}");

            return $loan->load(['copy.book', 'user']);
        });
    }

    /**
     * Return a book copy
     */
    public function return(Loan $loan): Loan
    {
        if ($loan->status !== 'active') {
            throw new \Exception('This loan is not active.');
        }

        return DB::transaction(function () use ($loan) {
            $loan->update([
                'returned_date' => Carbon::today(),
                'status' => 'returned',
                'returned_by' => auth()->id(),
            ]);

            $copy = $loan->copy;
            $copy->update(['status' => 'available']);

            // Check if there are pending holds for this book
            $this->processHoldsForBook($copy->book);

            // Check for overdue fines
            if ($loan->isOverdue()) {
                $fine = $this->createOverdueFine($loan);
                // Send fine notification
                $loan->user->notify(new FineNotification($fine));
            }

            AuditLog::log('return', 'Loan', $loan->id, "Book returned: {$copy->book->title}");

            return $loan->load(['copy.book', 'user']);
        });
    }

    /**
     * Renew a loan
     */
    public function renew(Loan $loan, int $additionalDays = 14): Loan
    {
        if ($loan->status !== 'active') {
            throw new \Exception('Only active loans can be renewed.');
        }

        if ($loan->copy->book->holds()->where('status', 'pending')->exists()) {
            throw new \Exception('Cannot renew: there are pending holds for this book.');
        }

        $loan->update([
            'due_date' => $loan->due_date->addDays($additionalDays),
        ]);

        AuditLog::log('renew', 'Loan', $loan->id, "Loan renewed until {$loan->due_date->format('Y-m-d')}");

        return $loan->load(['copy.book', 'user']);
    }

    /**
     * Place a hold on a book
     */
    public function placeHold(Book $book, User $user): Hold
    {
        if ($book->hasAvailableCopies()) {
            throw new \Exception('Book has available copies. No hold needed.');
        }

        if ($user->holds()->where('book_id', $book->id)->whereIn('status', ['pending', 'ready'])->exists()) {
            throw new \Exception('User already has an active hold for this book.');
        }

        $position = $book->holds()->where('status', 'pending')->count() + 1;

        $hold = Hold::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'requested_date' => Carbon::today(),
            'expiry_date' => Carbon::today()->addDays(7),
            'status' => 'pending',
            'position' => $position,
        ]);

        AuditLog::log('hold_placed', 'Hold', $hold->id, "Hold placed on: {$book->title}");

        return $hold->load(['book', 'user']);
    }

    /**
     * Cancel a hold
     */
    public function cancelHold(Hold $hold): void
    {
        if ($hold->status !== 'pending' && $hold->status !== 'ready') {
            throw new \Exception('Only pending or ready holds can be cancelled.');
        }

        DB::transaction(function () use ($hold) {
            $hold->update(['status' => 'cancelled']);

            // Recalculate positions for remaining holds
            $this->recalculateHoldPositions($hold->book);

            AuditLog::log('hold_cancelled', 'Hold', $hold->id, "Hold cancelled for: {$hold->book->title}");
        });
    }

    /**
     * Process holds when a book becomes available
     */
    protected function processHoldsForBook(Book $book): void
    {
        $nextHold = $book->holds()
            ->where('status', 'pending')
            ->orderBy('position')
            ->first();

        if ($nextHold && $book->hasAvailableCopies()) {
            $nextHold->update(['status' => 'ready']);
            $this->recalculateHoldPositions($book);
            // Send hold ready notification
            $nextHold->user->notify(new HoldReadyNotification($nextHold));
        }
    }

    /**
     * Recalculate hold positions for a book
     */
    protected function recalculateHoldPositions(Book $book): void
    {
        $holds = $book->holds()
            ->where('status', 'pending')
            ->orderBy('requested_date')
            ->get();

        foreach ($holds as $index => $hold) {
            $hold->update(['position' => $index + 1]);
        }
    }

    /**
     * Create an overdue fine
     */
    protected function createOverdueFine(Loan $loan): Fine
    {
        $daysOverdue = $loan->days_overdue;
        $fineAmount = $daysOverdue * 0.50; // $0.50 per day

        return Fine::create([
            'user_id' => $loan->user_id,
            'loan_id' => $loan->id,
            'amount' => $fineAmount,
            'type' => 'overdue',
            'status' => 'pending',
            'due_date' => Carbon::today()->addDays(30),
            'description' => "Overdue fine for {$daysOverdue} days",
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Mark a loan as lost
     */
    public function markAsLost(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan->update(['status' => 'lost']);

            $loan->copy->update(['status' => 'missing']);

            // Create lost book fine
            $fine = Fine::create([
                'user_id' => $loan->user_id,
                'loan_id' => $loan->id,
                'amount' => 50.00, // Replacement cost
                'type' => 'lost',
                'status' => 'pending',
                'description' => 'Lost book replacement fee',
                'created_by' => auth()->id(),
            ]);

            // Send fine notification
            $loan->user->notify(new FineNotification($fine));

            AuditLog::log('marked_lost', 'Loan', $loan->id, "Loan marked as lost");
        });
    }
}

