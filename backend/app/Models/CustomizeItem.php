<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomizeItem extends Model
{
    protected $fillable = [
        'name',
        'category',
        'price',
        'image',
        'description',
        'inventory_item_id',
        'status',
        'is_approved'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
        'is_approved' => 'boolean'
    ];

    // Relationship to inventory item (optional)
    public function inventoryItem()
    {
        return $this->belongsTo(Product::class, 'inventory_item_id');
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        // If linked to inventory item, use its price; otherwise use own price
        $price = $this->inventoryItem ? $this->inventoryItem->price : ($this->price ?? 0);
        return '₱' . number_format($price, 2);
    }

    /**
     * Get display price (prefers inventory price)
     */
    public function getDisplayPriceAttribute(): float
    {
        return $this->inventoryItem ? $this->inventoryItem->price : ($this->price ?? 0);
    }

    /**
     * Check if item is active and approved
     */
    public function isAvailable(): bool
    {
        return $this->status && $this->is_approved;
    }

    /**
     * Scope: Get active items
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Get approved items
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: Get items by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
