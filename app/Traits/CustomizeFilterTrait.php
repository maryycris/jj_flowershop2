<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Support\Str;

trait CustomizeFilterTrait
{
    /**
     * Get filtered customize items for admin, clerk, and customer
     * Ensures all three use exactly the same data source and filtering
     */
    public function getCustomizeItems()
    {
        $categories = ['Fresh Flowers','Dried Flowers','Artificial Flowers','Floral Supplies','Packaging Materials'];
        
        // Filter out finished products (bouquets, arrangements, etc.)
        $excludeKeywords = ['bouquet', 'arrangement', 'basket', 'vase', 'harmony', 'bundle', 'set', 'collection'];
        
        $items = Product::whereIn('category', $categories)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
        
        // Keep wrappers and floral supplies even if they contain keywords like "bouquet"
        $safeCategories = ['Packaging Materials', 'Floral Supplies'];
        $items = $items->filter(function ($product) use ($excludeKeywords, $safeCategories) {
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

        // Normalize names to avoid duplicates caused by spacing/case
        $normalize = function ($value) {
            $v = trim($value ?? '');
            $v = preg_replace('/\s+/', ' ', $v);
            return mb_strtolower($v);
        };

        // Remove duplicates by normalized category + name
        $items = $items->unique(function ($product) use ($normalize) {
            return $normalize($product->category) . '|' . $normalize($product->name);
        })->sortBy(function ($p) use ($normalize) {
            return $normalize($p->category) . '|' . $normalize($p->name);
        })->values()->groupBy('category');
            
        return $items;
    }
    
    /**
     * Get the standard categories for customize
     */
    public function getCustomizeCategories()
    {
        return ['Fresh Flowers','Dried Flowers','Artificial Flowers','Floral Supplies','Packaging Materials'];
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

        // Keep wrappers and floral supplies even if they contain keywords like "bouquet"
        $safeCategories = ['Packaging Materials', 'Floral Supplies'];
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