<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stamps_count',
        'status',
        'last_earned_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stamps()
    {
        return $this->hasMany(LoyaltyStamp::class);
    }

    public function redemptions()
    {
        return $this->hasMany(LoyaltyRedemption::class);
    }

    public function adjustments()
    {
        return $this->hasMany(LoyaltyAdjustment::class);
    }

    /**
     * Get formatted stamps count
     */
    public function getFormattedStampsCountAttribute(): string
    {
        return $this->stamps_count . ' / 5';
    }

    /**
     * Check if card is eligible for redemption
     */
    public function isEligibleForRedemption(): bool
    {
        return $this->stamps_count >= 5;
    }

    /**
     * Get stamps needed for next redemption
     */
    public function getStampsNeededAttribute(): int
    {
        return max(0, 5 - $this->stamps_count);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): int
    {
        return min(100, ($this->stamps_count / 5) * 100);
    }

    /**
     * Check if card is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope: Get active loyalty cards
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}


