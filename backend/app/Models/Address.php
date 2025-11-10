<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'label',
        'first_name',
        'last_name',
        'email',
        'company',
        'street_address',
        'barangay',
        'municipality',
        'city',
        'province',
        'zip_code',
        'region',
        'phone_number',
        'is_default',
        'landmark',
        'special_instructions',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the address
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full address as string
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->barangay,
            $this->municipality,
            $this->city,
            $this->province,
            $this->zip_code,
        ]);
        return implode(', ', $parts);
    }

    /**
     * Get address label with full address
     */
    public function getAddressWithLabelAttribute(): string
    {
        return $this->label . ': ' . $this->full_address;
    }

    /**
     * Check if this is the default address
     */
    public function isDefault(): bool
    {
        return $this->is_default === true;
    }

    /**
     * Mark this address as default
     */
    public function markAsDefault(): void
    {
        // Unset other defaults for this user
        Address::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        $this->update(['is_default' => true]);
    }

    /**
     * Scope: Get default addresses
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope: Get addresses for user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }
}
