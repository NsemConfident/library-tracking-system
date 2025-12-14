<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DueDateReminder extends Notification implements ShouldQueue
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
        $daysUntilDue = now()->diffInDays($this->loan->due_date, false);
        $bookTitle = $this->loan->copy->book->title;

        return (new MailMessage)
            ->subject('Library Book Due Date Reminder')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("This is a reminder that your book **{$bookTitle}** is due in {$daysUntilDue} day(s).")
            ->line("**Due Date:** {$this->loan->due_date->format('F j, Y')}")
            ->line('Please return the book on or before the due date to avoid late fees.')
            ->action('View My Loans', url('/library/my-loans'))
            ->line('Thank you for using our library!');
    }
}
