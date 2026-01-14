<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    protected $booking;

    protected $status; // 'approved' or 'rejected'

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $status)
    {
        $this->booking = $booking;
        $this->status = $status;
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
        $subject = match($this->status) {
            'approved' => 'Your Booking Has Been Approved!',
            'rejected' => 'Update on Your Booking Request',
            'submitted' => 'Booking Request Submitted',
            default => 'Booking Notification',
        };

        $statusText = match($this->status) {
            'submitted' => 'successfully submitted and is pending approval',
            default => 'been '.$this->status,
        };

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your booking for '.$this->booking->room->name.' has '.$statusText.'.');

        if ($this->status === 'rejected' && $this->booking->rejection_reason) {
            $message->line('Reason: '.$this->booking->rejection_reason);
        }

        return $message
            ->action('View Bookings', route('bookings.my-bookings'))
            ->line('Thank you for using our service!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $message = match($this->status) {
            'submitted' => 'Your booking for '.$this->booking->room->name.' has been submitted.',
            default => 'Your booking for '.$this->booking->room->name.' has been '.$this->status.'.',
        };

        return [
            'booking_id' => $this->booking->id,
            'room_name' => $this->booking->room->name,
            'status' => $this->status,
            'message' => $message,
            'type' => 'booking_status',
            'link' => route('bookings.my-bookings'),
        ];
    }
}
