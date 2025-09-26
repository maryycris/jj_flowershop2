<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Event;

class EventStatusChanged extends Notification
{
    use Queueable;

    protected $event;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, $oldStatus, $newStatus)
    {
        $this->event = $event;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $statusMessages = [
            'pending' => 'is pending approval',
            'confirmed' => 'has been confirmed',
            'completed' => 'has been completed',
            'cancelled' => 'has been cancelled'
        ];

        $message = $statusMessages[$this->newStatus] ?? 'status has been updated';
        
        return (new MailMessage)
            ->subject('Event Status Update - ' . ucfirst(str_replace('_', ' ', $this->event->event_type)) . ' Event')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your ' . $this->event->event_type . ' event scheduled for ' . 
                   \Carbon\Carbon::parse($this->event->event_date)->format('F j, Y') . 
                   ' ' . $message . '.')
            ->line('Event Details:')
            ->line('• Event Type: ' . ucfirst(str_replace('_', ' ', $this->event->event_type)))
            ->line('• Date: ' . \Carbon\Carbon::parse($this->event->event_date)->format('F j, Y'))
            ->line('• Time: ' . \Carbon\Carbon::parse($this->event->event_time)->format('g:i A'))
            ->line('• Venue: ' . $this->event->venue)
            ->line('• Total Amount: ₱' . number_format($this->event->total, 2))
            ->action('View Event Details', route('customer.events.show', $this->event->id))
            ->line('Thank you for choosing JJ Flower Shop!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusMessages = [
            'pending' => 'is pending approval',
            'confirmed' => 'has been confirmed',
            'completed' => 'has been completed',
            'cancelled' => 'has been cancelled'
        ];

        $message = $statusMessages[$this->newStatus] ?? 'status has been updated';
        
        return [
            'event_id' => $this->event->id,
            'event_type' => $this->event->event_type,
            'event_date' => $this->event->event_date,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => 'Your ' . $this->event->event_type . ' event scheduled for ' . 
                        \Carbon\Carbon::parse($this->event->event_date)->format('F j, Y') . 
                        ' ' . $message . '.',
            'title' => 'Event Status Updated'
        ];
    }
}
