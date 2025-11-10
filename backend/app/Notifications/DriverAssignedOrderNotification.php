<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DriverAssignedOrderNotification extends Notification
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
            'type' => 'driver_assigned_order',
            'title' => 'New Delivery Assigned',
            'message' => "Order #{$this->order->id} has been assigned to you. Please review and accept.",
            'order_id' => $this->order->id,
            'icon' => 'bi bi-truck',
            'color' => 'success',
            'action_url' => route('driver.orders.show', $this->order->id),
        ];
    }
}


