<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Order;

class OrderPaymentValidatedNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'payment_validated',
            'title' => 'Payment Validated',
            'message' => 'Your payment proof for Order #' . $this->order->id . ' has been approved. Your order is now being processed.',
            'order_id' => $this->order->id,
            'icon' => 'fas fa-credit-card',
            'color' => 'success',
            'action_url' => route('customer.orders.index', ['status' => 'to_ship'])
        ];
    }
} 