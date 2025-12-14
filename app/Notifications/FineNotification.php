<?php

namespace App\Notifications;

use App\Models\Fine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Fine $fine
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $fineAmount = number_format($this->fine->amount, 2);
        $fineType = ucfirst($this->fine->type);
        $dueDate = $this->fine->due_date ? $this->fine->due_date->format('F j, Y') : 'N/A';
        
        $bookTitle = 'N/A';
        if ($this->fine->loan && $this->fine->loan->copy) {
            $bookTitle = $this->fine->loan->copy->book->title;
        }

        $message = (new MailMessage)
            ->subject('Library Fine Notice')
            ->greeting('Hello ' . $notifiable->name . ',');

        if ($this->fine->type === 'overdue') {
            $message->line("A fine has been assessed for an overdue book: **{$bookTitle}**");
        } else {
            $message->line("A fine has been assessed: **{$fineType}**");
        }

        $message->line("**Fine Amount:** $" . $fineAmount)
            ->line("**Fine Type:** {$fineType}")
            ->line("**Due Date:** {$dueDate}");

        if ($this->fine->description) {
            $message->line("**Description:** {$this->fine->description}");
        }

        $message->line('Please contact the library to pay this fine.')
            ->action('View My Fines', url('/library/my-fines'))
            ->line('Thank you for your attention to this matter.');

        return $message;
    }
}
