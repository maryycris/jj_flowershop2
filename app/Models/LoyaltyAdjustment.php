<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_card_id',
        'adjusted_by',
        'delta',
        'previous_count',
        'new_count',
        'reason',
    ];

    public function card()
    {
        return $this->belongsTo(LoyaltyCard::class, 'loyalty_card_id');
    }

    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }
}

