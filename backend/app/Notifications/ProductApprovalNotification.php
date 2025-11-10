<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Product;
use App\Models\CatalogProduct;
use App\Models\User;

class ProductApprovalNotification extends Notification
{
    use Queueable;

    protected $product;
    protected $clerk;
    protected $changes;
    protected $action;

    /**
     * Create a new notification instance.
     *
     * @param Product|CatalogProduct $product
     * @param User $clerk
     * @param array|null $changes
     * @param string $action
     */
    public function __construct($product, User $clerk, $action = 'added', $changes = null)
    {
        // Validate that the product is either Product or CatalogProduct
        if (!($product instanceof Product) && !($product instanceof CatalogProduct)) {
            throw new \InvalidArgumentException('Product must be an instance of Product or CatalogProduct');
        }
        
        $this->product = $product;
        $this->clerk = $clerk;
        $this->action = $action;
        $this->changes = $changes;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'product_approval',
            'title' => 'Product ' . ucfirst($this->action),
            'message' => $this->action === 'added'
                ? "Clerk {$this->clerk->name} submitted a new product for approval: {$this->product->name}."
                : "Clerk {$this->clerk->name} edited product {$this->product->name}. Changes: " . json_encode($this->changes),
            'product_id' => $this->product->id,
            'clerk_id' => $this->clerk->id,
            'clerk_name' => $this->clerk->name,
            'action' => $this->action,
            'product_name' => $this->product->name,
            'action_url' => route('admin.products.index'),
            'icon' => 'fas fa-box',
            'color' => 'primary',
            'created_at' => now()->toISOString()
        ];
    }
} 