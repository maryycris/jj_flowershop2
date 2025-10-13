<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => $this->data['type'],
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'order_id' => $this->data['order_id'] ?? null,
            'action_url' => $this->data['action_url'] ?? null,
            'icon' => $this->data['icon'] ?? 'fas fa-bell',
            'color' => $this->data['color'] ?? 'primary',
            'created_at' => now()->toISOString()
        ];
    }
}
