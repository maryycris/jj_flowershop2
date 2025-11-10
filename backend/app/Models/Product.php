<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'stock',
        'category',
        'image',
        'is_marked_for_deletion',
        'marked_for_deletion_by',
        'marked_for_deletion_at',
        'image2',
        'image3',
        'status',
        'is_approved',
        'is_customize_item',
        'cost_price',
        'reorder_min',
        'reorder_max',
        'qty_consumed',
        'qty_damaged',
        'qty_sold',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
        'reorder_min' => 'integer',
        'reorder_max' => 'integer',
        'qty_consumed' => 'integer',
        'qty_damaged' => 'integer',
        'qty_sold' => 'integer',
        'status' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function orderProducts()
    {
        return $this->belongsToMany(Order::class, 'order_product')->withPivot('quantity');
    }

    public function compositions()
    {
        return $this->hasMany(ProductComposition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Get the full URL for the product image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Get formatted cost price
     */
    public function getFormattedCostPriceAttribute(): string
    {
        return '₱' . number_format($this->cost_price ?? 0, 2);
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Check if product needs reorder
     */
    public function needsReorder(): bool
    {
        return $this->reorder_min && $this->stock <= $this->reorder_min;
    }

    /**
     * Check if product is low stock
     */
    public function isLowStock(): bool
    {
        return $this->stock <= ($this->reorder_min ?? 5);
    }

    /**
     * Scope: Get products by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Get approved products
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: Get low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock <= COALESCE(reorder_min, 5)');
    }
}
