<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotedBanner extends Model
{
    protected $fillable = ['image','title','link_url','is_active','sort_order'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get the full URL for the banner image
     * Works with both local storage and Cloudinary
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/logo.png'); // Fallback image
        }

        // If image is already a full URL (Cloudinary), return it
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Use Storage to get the correct URL (works for both local and Cloudinary)
        try {
            return \Storage::disk('public')->url($this->image);
        } catch (\Exception $e) {
            // Fallback to asset if Storage fails
            return asset('storage/' . $this->image);
        }
    }
}
