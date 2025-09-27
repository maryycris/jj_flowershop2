<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'stock',
        'category',
        'image',
        'image2',
        'image3',
        'status',
        'is_approved',
        'cost_price',
        'reorder_min',
        'reorder_max',
        'qty_consumed',
        'qty_damaged',
        'qty_sold',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
        'reorder_min' => 'integer',
        'reorder_max' => 'integer',
        'qty_consumed' => 'integer',
        'qty_damaged' => 'integer',
        'qty_sold' => 'integer',
        'status' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function orderProducts()
    {
        return $this->belongsToMany(Order::class, 'order_product')->withPivot('quantity');
    }

    public function compositions()
    {
        return $this->hasMany(ProductComposition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
