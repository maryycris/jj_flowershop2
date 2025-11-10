<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class ReturnApprovedNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Return Approved - Order #{$this->order->id} - J'J Flower Shop")
            ->greeting('Hello ' . $this->order->user->name . ',')
            ->line("Great news! Your return request for order #{$this->order->id} has been approved.")
            ->line("**Return Reason:** {$this->order->return_reason}")
            ->line("**Order Total:** ₱" . number_format($this->order->total_price, 2))
            ->line('We will process your refund within 3-5 business days. You will receive a confirmation email once the refund is processed.')
            ->action('View Order Details', route('customer.orders.show', $this->order->id))
            ->line('Thank you for your patience and understanding.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'return_approved',
            'title' => "Return Approved - Order #{$this->order->id}",
            'message' => "Your return request for order #{$this->order->id} has been approved. Refund will be processed within 3-5 business days.",
            'order_id' => $this->order->id,
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'action_url' => route('customer.orders.show', $this->order->id)
        ];
    }
}