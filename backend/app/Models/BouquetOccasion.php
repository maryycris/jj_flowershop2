<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BouquetOccasion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color_theme',
        'recommended_flowers',
        'recommended_wrappers',
        'recommended_ribbons',
        'base_price',
        'is_active'
    ];

    protected $casts = [
        'recommended_flowers' => 'array',
        'recommended_wrappers' => 'array',
        'recommended_ribbons' => 'array',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->base_price, 2);
    }

    public function getRecommendedProductsAttribute()
    {
        $products = collect();
        
        if ($this->recommended_flowers) {
            $flowers = Product::whereIn('name', $this->recommended_flowers)
                ->where('category', 'Focal')
                ->get();
            $products = $products->merge($flowers);
        }
        
        if ($this->recommended_wrappers) {
            $wrappers = Product::whereIn('name', $this->recommended_wrappers)
                ->where('category', 'Wrapper')
                ->get();
            $products = $products->merge($wrappers);
        }
        
        if ($this->recommended_ribbons) {
            $ribbons = Product::whereIn('name', $this->recommended_ribbons)
                ->where('category', 'Ribbons')
                ->get();
            $products = $products->merge($ribbons);
        }
        
        return $products;
    }

    /**
     * Check if occasion is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Get formatted base price
     */
    public function getFormattedBasePriceAttribute(): string
    {
        return '₱' . number_format($this->base_price ?? 0, 2);
    }

    /**
     * Scope: Get active occasions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get occasions by slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}