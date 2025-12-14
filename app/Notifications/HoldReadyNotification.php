<?php

namespace App\Notifications;

use App\Models\Hold;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HoldReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Hold $hold
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bookTitle = $this->hold->book->title;
        $expiryDate = $this->hold->expiry_date->format('F j, Y');

        return (new MailMessage)
            ->subject('Your Reserved Book is Ready for Pickup')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Great news! The book you requested is now available: **{$bookTitle}**")
            ->line("Your hold is ready for checkout. Please visit the library to pick up your book.")
            ->line("**Hold Expires:** {$expiryDate}")
            ->line('You have 7 days to check out the book before the hold expires.')
            ->action('View Book Details', url('/library/books/' . $this->hold->book->id))
            ->action('View My Holds', url('/library/my-holds'))
            ->line('Thank you for using our library!');
    }
}
