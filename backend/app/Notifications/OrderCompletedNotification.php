<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCompletedNotification extends Notification
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
            'type' => 'order_completed',
            'title' => "Order #{$this->order->id} Completed",
            'message' => "Your order #{$this->order->id} has been completed! Thank you for your purchase.",
            'order_id' => $this->order->id,
            'icon' => 'fas fa-check-double',
            'color' => 'success',
            'action_url' => route('customer.orders.index', ['status' => 'receive'])
        ];
    }
}
