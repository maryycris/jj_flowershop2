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
}
