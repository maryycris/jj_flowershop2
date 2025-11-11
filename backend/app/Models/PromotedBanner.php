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

        // If image is already a full URL (Cloudinary), return it directly
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // If it's a path (not a full URL), check if Cloudinary is configured
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        
        // If Cloudinary is configured and we have a path, construct Cloudinary URL
        // DON'T use Storage facade to avoid configuration errors
        if ($cloudName && $apiKey && $apiSecret && strpos($this->image, 'http') !== 0) {
            // Construct Cloudinary URL from path
            // Path format: promoted_banners/xxx.png
            // Cloudinary URL format: https://res.cloudinary.com/{cloud_name}/image/upload/{path}
            $cloudinaryUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$this->image}";
            return $cloudinaryUrl;
        }

        // Fallback: If Cloudinary not configured, return null so frontend can handle fallback
        // This prevents 404 errors from /storage/ paths on Railway
        return null;
    }
}
