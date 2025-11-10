<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductChangesApprovedNotification extends Notification
{
    use Queueable;

    protected $product;
    protected $productName;

    public function __construct($product, $productName = null)
    {
        $this->product = $product;
        $this->productName = $productName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $productName = $this->productName ?? ($this->product->name ?? 'Product');
        
        return [
            'type' => 'product_changes_approved',
            'title' => 'Product Changes Approved',
            'message' => "Your product change request for '{$productName}' has been approved.",
            'product_id' => $this->product instanceof \App\Models\CatalogProduct ? $this->product->id : ($this->product->id ?? null),
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'action_url' => route('product_catalog.index')
        ];
    }
}
