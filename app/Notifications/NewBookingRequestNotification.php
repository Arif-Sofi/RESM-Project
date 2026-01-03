<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingRequestNotification extends Notification
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Booking Request Received')
            ->greeting('Hello Admin,')
            ->line('A new booking request has been submitted by '.$this->booking->user->name.'.')
            ->line('Room: '.$this->booking->room->name)
            ->line('Purpose: '.$this->booking->purpose)
            ->action('Review Request', route('admin.approvals'))
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'user_name' => $this->booking->user->name,
            'room_name' => $this->booking->room->name,
            'message' => 'New booking request from '.$this->booking->user->name.' for '.$this->booking->room->name.'.',
            'type' => 'new_booking_request',
            'link' => route('admin.approvals'),
        ];
    }
}
