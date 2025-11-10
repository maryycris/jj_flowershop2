<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'product_id', 'catalog_product_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function catalogProduct(): BelongsTo
    {
        return $this->belongsTo(\App\Models\CatalogProduct::class, 'catalog_product_id');
    }

    /**
     * Check if favorite is for a product
     */
    public function isProduct(): bool
    {
        return !empty($this->product_id);
    }

    /**
     * Check if favorite is for a catalog product
     */
    public function isCatalogProduct(): bool
    {
        return !empty($this->catalog_product_id);
    }

    /**
     * Get the favorited item
     */
    public function getItemAttribute()
    {
        if ($this->catalog_product_id) {
            return $this->catalogProduct;
        }
        return $this->product;
    }

    /**
     * Scope: Get favorites for user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get product favorites
     */
    public function scopeProducts($query)
    {
        return $query->whereNotNull('product_id');
    }

    /**
     * Scope: Get catalog product favorites
     */
    public function scopeCatalogProducts($query)
    {
        return $query->whereNotNull('catalog_product_id');
    }
}
