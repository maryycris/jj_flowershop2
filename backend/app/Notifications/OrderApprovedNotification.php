<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderApprovedNotification extends Notification
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
            'type' => 'order_approved',
            'title' => "Order #{$this->order->id} Approved",
            'message' => "Your order #{$this->order->id} has been approved and is ready for processing!",
            'order_id' => $this->order->id,
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'action_url' => route('customer.orders.index', ['status' => 'to_ship'])
        ];
    }
}
