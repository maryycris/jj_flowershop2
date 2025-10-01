<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomBouquet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bouquet_type',
        'wrapper',
        'focal_flower_1',
        'focal_flower_2',
        'focal_flower_3',
        'greenery',
        'filler',
        'ribbon',
        'money_amount',
        'quantity',
        'total_price',
        'customization_data',
        'is_active'
    ];

    protected $casts = [
        'customization_data' => 'array',
        'money_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->total_price, 2);
    }

    public function getBouquetDescriptionAttribute()
    {
        if ($this->bouquet_type === 'money') {
            return "Money Bouquet - ₱" . number_format($this->money_amount, 2);
        }

        $components = [];
        if ($this->wrapper) $components[] = "Wrapper: {$this->wrapper}";
        if ($this->focal_flower_1) $components[] = "Flower 1: {$this->focal_flower_1}";
        if ($this->focal_flower_2) $components[] = "Flower 2: {$this->focal_flower_2}";
        if ($this->focal_flower_3) $components[] = "Flower 3: {$this->focal_flower_3}";
        if ($this->greenery) $components[] = "Greenery: {$this->greenery}";
        if ($this->filler) $components[] = "Filler: {$this->filler}";
        if ($this->ribbon) $components[] = "Ribbon: {$this->ribbon}";

        return "Custom Bouquet - " . implode(', ', $components);
    }
}