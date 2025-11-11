<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image',
        'image2',
        'image3',
        'status',
        'is_approved',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     * This ensures image_url is always included in JSON responses.
     */
    protected $appends = ['image_url'];

    public function compositions()
    {
        return $this->hasMany(CatalogProductComposition::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Get the full URL for the image
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
            // Path format: catalog_products/8z56vsEINMLSYwRkt6nK...9Aso6MFwphyga.png
            // Cloudinary URL format: https://res.cloudinary.com/{cloud_name}/image/upload/{path}
            // Note: Old images stored as paths may not exist in Cloudinary, but we'll try anyway
            // The browser's onerror handler will show the logo if the image doesn't exist
            $cloudinaryUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$this->image}";
            return $cloudinaryUrl;
        }

        // Fallback: If Cloudinary not configured, return null so frontend can handle fallback
        // This prevents 404 errors from /storage/ paths on Railway
        return null;
    }

    /**
     * Get image URL for image2
     */
    public function getImage2UrlAttribute()
    {
        if (!$this->image2) {
            return null;
        }

        if (filter_var($this->image2, FILTER_VALIDATE_URL)) {
            return $this->image2;
        }

        // If it's a path (not a full URL), check if Cloudinary is configured
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        
        // If Cloudinary is configured and we have a path, construct Cloudinary URL
        // DON'T use Storage facade to avoid configuration errors
        if ($cloudName && $apiKey && $apiSecret && strpos($this->image2, 'http') !== 0) {
            // Construct Cloudinary URL from path
            $cloudinaryUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$this->image2}";
            return $cloudinaryUrl;
        }

        // Fallback to local storage path (only if Cloudinary not configured)
        return asset('storage/' . $this->image2);
    }

    /**
     * Get image URL for image3
     */
    public function getImage3UrlAttribute()
    {
        if (!$this->image3) {
            return null;
        }

        if (filter_var($this->image3, FILTER_VALIDATE_URL)) {
            return $this->image3;
        }

        // If it's a path (not a full URL), check if Cloudinary is configured
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        
        // If Cloudinary is configured and we have a path, construct Cloudinary URL
        // DON'T use Storage facade to avoid configuration errors
        if ($cloudName && $apiKey && $apiSecret && strpos($this->image3, 'http') !== 0) {
            // Construct Cloudinary URL from path
            $cloudinaryUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$this->image3}";
            return $cloudinaryUrl;
        }

        // Fallback to local storage path (only if Cloudinary not configured)
        return asset('storage/' . $this->image3);
    }
}
