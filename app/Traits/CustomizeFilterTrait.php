<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\CustomizeItem;
use Illuminate\Support\Str;

trait CustomizeFilterTrait
{
    /**
     * Get filtered customize items for admin, clerk, and customer
     * Ensures all three use exactly the same data source and filtering
     */
    public function getCustomizeItems()
    {
        // Get customize items from the separate customize_items table
        $items = CustomizeItem::where('status', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
            
        return $items;
    }
    
    /**
     * Get the standard categories for customize
     */
    public function getCustomizeCategories()
    {
        return ['Fresh Flowers','Greenery','Artificial Flowers','Ribbon','Wrappers'];
    }
    
    /**
     * Get filtered items for price calculation (same filtering as display)
     */
    public function getCustomizeItemsForPricing()
    {
        $categories = $this->getCustomizeCategories();
        
        // Filter out finished products
        $excludeKeywords = ['bouquet', 'arrangement', 'basket', 'vase', 'harmony', 'bundle', 'set', 'collection'];
        
        $products = Product::whereIn('category', $categories)
            ->get();

        // Keep wrappers and ribbons even if they contain keywords like "bouquet"
        $safeCategories = ['Wrappers', 'Ribbon'];
        $products = $products->filter(function ($product) use ($excludeKeywords, $safeCategories) {
            if (in_array($product->category, $safeCategories, true)) {
                return true;
            }
            $nameLower = mb_strtolower($product->name);
            foreach ($excludeKeywords as $kw) {
                if (str_contains($nameLower, mb_strtolower($kw))) {
                    return false;
                }
            }
            return true;
        });

        // Normalizer to avoid undefined variable and ensure consistency
        $normalize = function ($value) {
            $v = trim($value ?? '');
            $v = preg_replace('/\s+/', ' ', $v);
            return mb_strtolower($v);
        };

        // Remove duplicates by normalized category + name
        $products = $products->unique(function ($product) use ($normalize) {
            return $normalize($product->category) . '|' . $normalize($product->name);
        })->sortBy(function ($p) use ($normalize) {
            return $normalize($p->category) . '|' . $normalize($p->name);
        })->values();

        return $products->keyBy('name');
    }
}