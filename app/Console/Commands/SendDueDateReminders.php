<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Notifications\DueDateReminder;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDueDateReminders extends Command
{
    protected $signature = 'library:send-due-date-reminders {--days= : Number of days before due date to send reminder}';

    protected $description = 'Send due date reminder emails to patrons';

    public function handle()
    {
        $settings = app(SettingsService::class);
        $daysBeforeDue = $this->option('days') 
            ? (int) $this->option('days') 
            : $settings->getDueDateReminderDays();
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
