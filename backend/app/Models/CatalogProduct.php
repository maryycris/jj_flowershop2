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
        $driver = config('filesystems.disks.public.driver');
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        
        // If using Cloudinary and we have a path, construct Cloudinary URL
        if ($driver === 'cloudinary' && $cloudName && strpos($this->image, 'http') !== 0) {
            // Construct Cloudinary URL from path
            // Path format: catalog_products/8z56vsEINMLSYwRkt6nK...9Aso6MFwphyga.png
            // Cloudinary URL format: https://res.cloudinary.com/{cloud_name}/image/upload/{path}
            $cloudinaryUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$this->image}";
            \Log::info('Constructed Cloudinary URL from path', [
                'path' => $this->image,
                'url' => $cloudinaryUrl
            ]);
            return $cloudinaryUrl;
        }

        // For local storage, use Storage facade or asset
        try {
            $url = \Storage::disk('public')->url($this->image);
            // If Storage returns a local path that doesn't look like Cloudinary, use asset
            if (strpos($url, 'cloudinary.com') === false && strpos($url, 'res.cloudinary.com') === false) {
                return asset('storage/' . $this->image);
            }
            return $url;
        } catch (\Exception $e) {
            // Fallback to asset if Storage fails
            return asset('storage/' . $this->image);
        }
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

        try {
            return \Storage::disk('public')->url($this->image2);
        } catch (\Exception $e) {
            return asset('storage/' . $this->image2);
        }
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

        try {
            return \Storage::disk('public')->url($this->image3);
        } catch (\Exception $e) {
            return asset('storage/' . $this->image3);
        }
    }
}
