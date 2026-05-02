<?php

namespace App\Livewire\Circulation;

use App\Models\Copy;
use App\Models\LibrarySession;
use App\Models\Loan;
use App\Services\LibraryService;
use App\Services\LibrarySessionService;
use App\Services\SettingsService;
use Livewire\Component;

class Checkout extends Component
{
    public $barcode = '';
    public $loanDays;
    public $mode = 'borrow';
    public $activeSessionUserId = null;

    public function mount(): void
    {
        $this->loanDays = app(SettingsService::class)->getLoanPeriodDays();

        $activeSession = LibrarySession::with('user')
            ->where('status', 'active')
            ->latest('check_in_time')
            ->first();

        $this->activeSessionUserId = $activeSession?->user_id;
    }

    public function setMode(string $mode): void
    {
        if (!in_array($mode, ['borrow', 'return'], true)) {
            return;
        }

        $this->mode = $mode;
    }

    public function getActiveSessionProperty(): ?LibrarySession
    {
        if (!$this->activeSessionUserId) {
            return null;
        }

        return LibrarySession::with('user')
            ->where('user_id', $this->activeSessionUserId)
            ->where('status', 'active')
            ->latest('check_in_time')
            ->first();
    }

    public function handleRfidScan(string $uid): void
    {
        $uid = strtoupper(trim($uid));

        if ($uid === '') {
            return;
        }

        $this->barcode = $uid;

        try {
            $sessionService = app(LibrarySessionService::class);

            $user = \App\Models\User::where('rfid_uid', $uid)->first();
            if ($user) {
                $this->handleStudentScan($sessionService, $user);
                return;
            }

            $copy = Copy::where('barcode', $uid)->first();
            if ($copy) {
                $this->handleBookScan($sessionService, $copy);
                return;
            }

            throw new \Exception("Scanned UID '{$uid}' is not assigned to any student or book copy.");
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    protected function handleStudentScan(LibrarySessionService $sessionService, \App\Models\User $user): void
    {
        $activeSession = $sessionService->getActiveSession($user);

        if ($activeSession) {
            $sessionService->endSession($user);

            if ($this->activeSessionUserId === $user->id) {
                $this->activeSessionUserId = null;
            }

            $this->dispatch('toast', [
                'message' => "{$user->name} checked out from the library.",
                'type' => 'success',
            ]);

            return;
        }

        $sessionService->startSession($user);
        $this->activeSessionUserId = $user->id;

        $this->dispatch('toast', [
            'message' => "{$user->name} checked in. Select mode and scan book RFID.",
            'type' => 'success',
        ]);
    }

    protected function handleBookScan(LibrarySessionService $sessionService, Copy $copy): void
    {
        $activeSession = $this->activeSession;
        $sessionService->validateSession($activeSession);
        $student = $activeSession->user;

        if ($this->mode === 'borrow') {
            $loan = app(LibraryService::class)->checkout($copy, $student, (int) $this->loanDays);

            $this->dispatch('toast', [
                'message' => "Book '{$loan->copy->book->title}' borrowed by {$student->name}.",
                'type' => 'success',
            ]);

            return;
        }

        $loan = Loan::where('copy_id', $copy->id)
            ->where('status', 'active')
            ->firstOrFail();

        $loan = app(LibraryService::class)->return($loan);

        $this->dispatch('toast', [
            'message' => "Book '{$loan->copy->book->title}' returned successfully.",
            'type' => 'success',
        ]);
    }

    public function render()
    {
        return view('livewire.circulation.checkout', [
            'activeSession' => $this->activeSession,
        ])->layout('components.layouts.app.sidebar', [
            'title' => __('RFID Circulation'),
        ]);
    }
}
