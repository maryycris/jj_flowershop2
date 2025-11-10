<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomBouquet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bouquet_type',
        'wrapper',
        'focal_flower_1',
        'focal_flower_2',
        'focal_flower_3',
        'greenery',
        'filler',
        'ribbon',
        'money_amount',
        'quantity',
        'total_price',
        'customization_data',
        'preview_image',
        'is_active'
    ];

    protected $casts = [
        'customization_data' => 'array',
        'money_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->total_price, 2);
    }

    public function getBouquetDescriptionAttribute()
    {
        if ($this->bouquet_type === 'money') {
            return "Money Bouquet - ₱" . number_format($this->money_amount, 2);
        }

        $components = [];
        if ($this->wrapper) $components[] = "Wrapper: {$this->wrapper}";
        if ($this->focal_flower_1) $components[] = "Flower 1: {$this->focal_flower_1}";
        if ($this->focal_flower_2) $components[] = "Flower 2: {$this->focal_flower_2}";
        if ($this->focal_flower_3) $components[] = "Flower 3: {$this->focal_flower_3}";
        if ($this->greenery) $components[] = "Greenery: {$this->greenery}";
        if ($this->filler) $components[] = "Filler: {$this->filler}";
        if ($this->ribbon) $components[] = "Ribbon: {$this->ribbon}";

        return "Custom Bouquet - " . implode(', ', $components);
    }

    /**
     * Get the preview image for the custom bouquet
     * Returns the saved composite preview image, or generates one on-the-fly
     */
    public function getPreviewImageAttribute($value)
    {
        // If preview_image is saved and exists, check if it's a composite image
        // Composite images are in 'custom_bouquets/' directory
        if ($value && file_exists(storage_path('app/public/' . $value))) {
            // If it's already a composite image (in custom_bouquets folder), use it
            if (strpos($value, 'custom_bouquets/') === 0) {
                return asset('storage/' . $value);
            }
            // If it's a component image (wrapper, etc), regenerate composite
        }
        
        if ($this->bouquet_type === 'money') {
            return asset('images/landingpage_bouquet/bokk.png'); // Use existing money bouquet image
        }

        // For regular bouquets without saved preview, generate composite image
        try {
            // Check if GD extension and required functions are available
            if (extension_loaded('gd') && function_exists('imagecreatetruecolor') && function_exists('imagecreatefrompng')) {
                $imageService = new \App\Services\CustomBouquetImageService();
                $previewPath = $imageService->generateCompositeImage($this);
                
                if ($previewPath && file_exists(storage_path('app/public/' . $previewPath))) {
                    // Save the generated image path (update without triggering accessor)
                    \DB::table('custom_bouquets')
                        ->where('id', $this->id)
                        ->update(['preview_image' => $previewPath]);
                    
                    // Refresh attributes
                    $this->attributes['preview_image'] = $previewPath;
                    
                    return asset('storage/' . $previewPath);
                } else {
                    \Log::warning('Preview image generated but file not found for bouquet #' . $this->id);
                }
            } else {
                \Log::warning('GD extension or required functions not available for bouquet #' . $this->id);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to generate composite preview image for bouquet #' . $this->id . ': ' . $e->getMessage() . ' | Trace: ' . substr($e->getTraceAsString(), 0, 200));
        }
        
        // If generation fails, try to show a better fallback - combine multiple component images if possible
        // For now, use the wrapper as fallback (but this should rarely happen if generation works)
        $items = \App\Models\CustomizeItem::where('status', true)->get();
        
        // Try to get wrapper first (most visible component)
        if ($this->wrapper) {
            $item = $items->firstWhere('name', $this->wrapper);
            if ($item && $item->image && file_exists(storage_path('app/public/' . $item->image))) {
                return asset('storage/' . $item->image);
            }
        }
        
        // Fallback to generic bouquet image
        return asset('images/landingpage_bouquet/bokk.png');
    }

    /**
     * Get formatted money amount
     */
    public function getFormattedMoneyAmountAttribute(): string
    {
        return '₱' . number_format($this->money_amount ?? 0, 2);
    }

    /**
     * Get unit price (total price / quantity)
     */
    public function getUnitPriceAttribute(): float
    {
        return $this->quantity > 0 ? $this->total_price / $this->quantity : 0;
    }

    /**
     * Check if bouquet is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Scope: Get active bouquets
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get bouquets by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('bouquet_type', $type);
    }

    /**
     * Get orders that include this custom bouquet
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_custom_bouquet')
            ->withPivot('quantity', 'rating', 'review_comment', 'reviewed', 'reviewed_at')
            ->withTimestamps();
    }
}