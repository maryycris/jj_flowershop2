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
}


