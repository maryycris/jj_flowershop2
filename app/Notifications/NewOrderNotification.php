<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
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
            'type' => 'new_order',
            'title' => 'New Order Received',
            'message' => "New order #{$this->order->id} has been placed by {$this->order->user->name}",
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'total_price' => $this->order->total_price,
            'icon' => 'fas fa-shopping-cart',
            'color' => 'success',
            'action_url' => route('admin.orders.show', $this->order->id)
        ];
    }
}
