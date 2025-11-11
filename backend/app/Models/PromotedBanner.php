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
            \Log::warning('Banner image URL accessor: No image set, returning logo', [
                'banner_id' => $this->id
            ]);
            return asset('images/logo.png'); // Fallback image
        }

        // If image is already a full URL (Cloudinary), return it directly
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            \Log::info('Banner image URL accessor: Returning full URL directly', [
                'banner_id' => $this->id,
                'image' => $this->image
            ]);
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
            \Log::info('Banner image URL accessor: Constructed Cloudinary URL from path', [
                'banner_id' => $this->id,
                'path' => $this->image,
                'url' => $cloudinaryUrl
            ]);
            return $cloudinaryUrl;
        }

        // Fallback: If Cloudinary not configured, return null so frontend can handle fallback
        // This prevents 404 errors from /storage/ paths on Railway
        \Log::warning('Banner image URL accessor: Returning null (Cloudinary not configured or invalid path)', [
            'banner_id' => $this->id,
            'image' => $this->image,
            'cloud_name_set' => !empty($cloudName),
            'api_key_set' => !empty($apiKey),
            'api_secret_set' => !empty($apiSecret)
        ]);
        return null;
    }
}
