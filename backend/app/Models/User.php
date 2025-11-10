<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasRole;

class User extends Authenticatable
{
    use Notifiable;
    use HasRole;

    protected $fillable = [
        'name',
        'email',
        'sex',
        'contact_number',
        'role',
        'username',
        'password',
        'store_name', // <-- use store_name instead of store_id
        'first_name',
        'last_name',
        'profile_picture',
        'address',
        // Social login and verification fields
        'google_id',
        'facebook_id',
        'verification_code',
        'verification_expires_at',
        'is_verified',
        'phone',
        // Address fields
        'street_address',
        'barangay',
        'municipality',
        'city',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verification_expires_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function deliveries()
    {
        return $this->hasMany(\App\Models\Delivery::class, 'driver_id');
    }

    public function driver()
    {
        return $this->hasOne(\App\Models\Driver::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the user's full address as a single string.
     */
    public function getAddressAttribute()
    {
        $parts = array_filter([
            $this->street_address,
            $this->barangay,
            $this->municipality,
            $this->city
        ]);
        return $parts ? implode(', ', $parts) : null;
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name || $this->last_name) {
            return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
        }
        return $this->name;
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        if ($this->profile_picture) {
            if (filter_var($this->profile_picture, FILTER_VALIDATE_URL)) {
                return $this->profile_picture;
            }
            return asset('storage/' . $this->profile_picture);
        }
        return asset('images/default-avatar.svg');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is clerk
     */
    public function isClerk(): bool
    {
        return $this->role === 'clerk';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user is driver
     */
    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    /**
     * Get formatted contact number
     */
    public function getFormattedContactNumberAttribute(): string
    {
        return $this->contact_number ?? $this->phone ?? 'N/A';
    }

    /**
     * Scope: Get users by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope: Get verified users
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Get loyalty card for user
     */
    public function loyaltyCard()
    {
        return $this->hasOne(LoyaltyCard::class);
    }

    /**
     * Get user's custom bouquets
     */
    public function customBouquets()
    {
        return $this->hasMany(CustomBouquet::class);
    }
}
