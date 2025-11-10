<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'custom_bouquet_id',
        'quantity',
        'item_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customBouquet()
    {
        return $this->belongsTo(CustomBouquet::class);
    }

    public function getItemAttribute()
    {
        if ($this->item_type === 'custom_bouquet') {
            return $this->customBouquet;
        }
        return $this->product;
    }

    public function getTotalPriceAttribute()
    {
        if ($this->item_type === 'custom_bouquet') {
            return $this->customBouquet ? $this->customBouquet->total_price * $this->quantity : 0;
        }
        return $this->product ? $this->product->price * $this->quantity : 0;
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return '₱' . number_format($this->total_price, 2);
    }

    /**
     * Get item name
     */
    public function getItemNameAttribute(): string
    {
        if ($this->item_type === 'custom_bouquet') {
            return $this->customBouquet ? 'Custom Bouquet' : 'N/A';
        }
        return $this->product ? $this->product->name : 'N/A';
    }

    /**
     * Check if cart item is a custom bouquet
     */
    public function isCustomBouquet(): bool
    {
        return $this->item_type === 'custom_bouquet';
    }

    /**
     * Check if cart item is a product
     */
    public function isProduct(): bool
    {
        return $this->item_type === 'product' || ($this->item_type === null && $this->product_id !== null);
    }

    /**
     * Scope: Get cart items for user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
