<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Loan $loan
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bookTitle = $this->loan->copy->book->title;
        $daysOverdue = $this->loan->days_overdue;
        $fineAmount = $daysOverdue * 0.50; // $0.50 per day

        return (new MailMessage)
            ->subject('Overdue Library Book Notice')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your book **{$bookTitle}** is overdue.")
            ->line("**Due Date:** {$this->loan->due_date->format('F j, Y')}")
            ->line("**Days Overdue:** {$daysOverdue} day(s)")
            ->line("**Accrued Fine:** $" . number_format($fineAmount, 2))
            ->line('Please return the book as soon as possible to avoid additional fines.')
            ->action('View My Loans', url('/library/my-loans'))
            ->line('Thank you for your prompt attention to this matter.');
    }
}
