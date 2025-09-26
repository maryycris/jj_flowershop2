<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Event $event, $oldStatus, $newStatus)
    {
        $this->event = $event;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $statusColors = [
            'pending' => '#ffc107',
            'confirmed' => '#28a745',
            'completed' => '#6c757d',
            'cancelled' => '#dc3545'
        ];

        $statusMessages = [
            'pending' => 'is pending approval',
            'confirmed' => 'has been confirmed',
            'completed' => 'has been completed',
            'cancelled' => 'has been cancelled'
        ];

        $color = $statusColors[$this->newStatus] ?? '#007bff';
        $message = $statusMessages[$this->newStatus] ?? 'status has been updated';

        return $this->subject('Event Status Update - ' . $this->event->event_type)
                    ->view('emails.event-status-changed')
                    ->with([
                        'event' => $this->event,
                        'oldStatus' => $this->oldStatus,
                        'newStatus' => $this->newStatus,
                        'color' => $color,
                        'message' => $message
                    ]);
    }
}
