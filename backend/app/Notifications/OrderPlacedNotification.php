<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'order_placed',
            'title' => "Order #{$this->order->id} Placed Successfully",
            'message' => "Your order #{$this->order->id} has been placed successfully!",
            'order_id' => $this->order->id,
            'total_price' => $this->order->total_price,
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'action_url' => route('customer.orders.index', ['status' => 'to_pay'])
        ];
    }
}
