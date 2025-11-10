<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InventoryChangesApprovedNotification extends Notification
{
    use Queueable;

    protected $changesCount;

    public function __construct($changesCount = 1)
    {
        $this->changesCount = $changesCount;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'inventory_changes_approved',
            'title' => 'Inventory Changes Approved',
            'message' => $this->changesCount > 1 
                ? "Your {$this->changesCount} inventory change requests have been approved."
                : "Your inventory change request has been approved.",
            'changes_count' => $this->changesCount,
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'action_url' => route('inventory.manage')
        ];
    }
}
