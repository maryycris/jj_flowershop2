<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderDeliveredNotification extends Notification
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
            'type' => 'order_delivered',
            'title' => 'Order Delivered',
            'message' => "Your order #{$this->order->id} has been delivered! Please confirm receipt.",
            'order_id' => $this->order->id,
            'icon' => 'fas fa-truck',
            'color' => 'info',
            'action_url' => route('customer.orders.index', ['status' => 'to_receive'])
        ];
    }
}
