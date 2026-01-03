<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification
{
    use Queueable;

    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
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
            ->subject('New Event Notification: '.$this->event->title)
            ->line('A new event has been created or you have been assigned to an event.')
            ->line('Title: '.$this->event->title)
            ->line('Location: '.$this->event->location)
            ->line('Starts At: '.$this->event->start_at->format('Y-m-d H:i'))
            ->action('View Calendar', route('events.index'))
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'location' => $this->event->location,
            'message' => 'New event: '.$this->event->title.' at '.($this->event->location ?? 'TBA'),
            'type' => 'event_created',
            'link' => route('events.index'),
        ];
    }
}
