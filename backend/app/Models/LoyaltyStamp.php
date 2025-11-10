<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyStamp extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_card_id',
        'order_id',
        'earned_at',
    ];

    public function card()
    {
        return $this->belongsTo(LoyaltyCard::class, 'loyalty_card_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get formatted earned date
     */
    public function getFormattedEarnedDateAttribute(): string
    {
        return $this->earned_at ? \Carbon\Carbon::parse($this->earned_at)->format('M d, Y') : 'N/A';
    }

    /**
     * Scope: Get stamps for loyalty card
     */
    public function scopeForCard($query, $cardId)
    {
        return $query->where('loyalty_card_id', $cardId);
    }

    /**
     * Scope: Get stamps by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('earned_at', [$startDate, $endDate]);
    }
}


