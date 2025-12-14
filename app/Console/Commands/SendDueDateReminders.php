<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Notifications\DueDateReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDueDateReminders extends Command
{
    protected $signature = 'library:send-due-date-reminders {--days=3 : Number of days before due date to send reminder}';

    protected $description = 'Send due date reminder emails to patrons';

    public function handle()
    {
        $daysBeforeDue = (int) $this->option('days');
        $reminderDate = Carbon::today()->addDays($daysBeforeDue);

        $loans = Loan::where('status', 'active')
            ->whereDate('due_date', $reminderDate)
            ->with(['user', 'copy.book'])
            ->get();

        $count = 0;
        foreach ($loans as $loan) {
            try {
                $loan->user->notify(new DueDateReminder($loan));
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for loan {$loan->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} due date reminder(s) for loans due on {$reminderDate->format('Y-m-d')}.");
        
        return Command::SUCCESS;
    }
}
