<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Notifications\OverdueNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendOverdueNotifications extends Command
{
    protected $signature = 'library:send-overdue-notifications {--days=1 : Send notifications for books overdue by this many days}';

    protected $description = 'Send overdue notification emails to patrons';

    public function handle()
    {
        $daysOverdue = (int) $this->option('days');
        $cutoffDate = Carbon::today()->subDays($daysOverdue);

        $loans = Loan::where('status', 'active')
            ->whereDate('due_date', '<=', $cutoffDate)
            ->with(['user', 'copy.book'])
            ->get();

        $count = 0;
        foreach ($loans as $loan) {
            try {
                $loan->user->notify(new OverdueNotification($loan));
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send overdue notification for loan {$loan->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} overdue notification(s) for loans overdue by {$daysOverdue} or more days.");
        
        return Command::SUCCESS;
    }
}
