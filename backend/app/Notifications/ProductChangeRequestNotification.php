<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductChangeRequestNotification extends Notification
{
    use Queueable;

    protected $pendingChange;
    protected $product;
    protected $clerk;

    public function __construct($pendingChange, $product = null, $clerk = null)
    {
        $this->pendingChange = $pendingChange;
        $this->product = $product;
        $this->clerk = $clerk;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $productName = $this->product ? $this->product->name : 'Product';
        $clerkName = $this->clerk ? $this->clerk->name : 'A clerk';
        $action = $this->pendingChange->action === 'edit' ? 'edited' : 'deleted';
        
        return [
            'type' => 'product_change_request',
            'title' => 'Product Change Request',
            'message' => $this->pendingChange->action === 'edit'
                ? "{$clerkName} has requested to edit product: {$productName}."
                : "{$clerkName} has requested to delete product: {$productName}.",
            'pending_change_id' => $this->pendingChange->id,
            'product_id' => $this->pendingChange->product_id,
            'action' => $this->pendingChange->action,
            'product_name' => $productName,
            'clerk_name' => $clerkName,
            'icon' => 'fas fa-edit',
            'color' => 'warning',
            'action_url' => route('admin.products.index')
        ];
    }
}
