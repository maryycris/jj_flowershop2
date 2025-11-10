<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InventoryChangeNotification extends Notification
{
    use Queueable;

    protected $changesCount;
    protected $submittedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($changesCount, $submittedBy)
    {
        $this->changesCount = $changesCount;
        $this->submittedBy = $submittedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'inventory_change',
            'title' => 'Inventory Changes Request',
            'message' => "A clerk has submitted {$this->changesCount} inventory changes for your review.",
            'changes_count' => $this->changesCount,
            'submitted_by' => $this->submittedBy,
            'submitted_at' => now()->toISOString(),
            'action_url' => route('admin.inventory.index') . '#inventory-logs',
            'icon' => 'fas fa-boxes',
            'color' => 'warning'
        ];
    }
}
