<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
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
            'type' => $this->data['type'] ?? 'general',
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'action_url' => $this->data['action_url'] ?? null,
            'icon' => $this->data['icon'] ?? 'fas fa-bell',
            'color' => $this->data['color'] ?? 'primary',
            'created_at' => now()->toISOString()
        ];
    }
}
