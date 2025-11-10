<?php

namespace App\Models;

use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => 'Low stock alert: ' . $this->product->name . ' (Code: ' . $this->product->code . ') is at or below minimum stock (Current: ' . $this->product->stock . ', Min: ' . $this->product->reorder_min . ')',
            'product_id' => $this->product->id,
            'action_url' => route('admin.inventory.index'),
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'warning',
            'created_at' => now()->toISOString()
        ];
    }
} 