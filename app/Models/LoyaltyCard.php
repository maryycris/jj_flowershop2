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
}


