<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_card_id',
        'order_id',
        'discount_amount',
        'redeemed_at',
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
     * Get formatted discount amount
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return '₱' . number_format($this->discount_amount ?? 0, 2);
    }

    /**
     * Get formatted redeemed date
     */
    public function getFormattedRedeemedDateAttribute(): string
    {
        return $this->redeemed_at ? \Carbon\Carbon::parse($this->redeemed_at)->format('M d, Y H:i') : 'N/A';
    }

    /**
     * Scope: Get redemptions for loyalty card
     */
    public function scopeForCard($query, $cardId)
    {
        return $query->where('loyalty_card_id', $cardId);
    }

    /**
     * Scope: Get redemptions by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('redeemed_at', [$startDate, $endDate]);
    }
}


