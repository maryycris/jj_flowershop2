<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCompletedClerkNotification extends Notification
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
            'message' => "Order #{$this->order->id} has been completed successfully.",
            'order_id' => $this->order->id,
            'icon' => 'fas fa-check-double',
            'color' => 'success',
            'action_url' => route('sales-orders.show', $this->order->id)
        ];
    }
}
