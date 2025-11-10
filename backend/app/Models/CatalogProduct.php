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
