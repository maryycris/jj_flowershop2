<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomizeItem extends Model
{
    protected $fillable = [
        'name',
        'category',
        'price',
        'image',
        'description',
        'inventory_item_id',
        'status',
        'is_approved'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
        'is_approved' => 'boolean'
    ];

    // Relationship to inventory item (optional)
    public function inventoryItem()
    {
        return $this->belongsTo(Product::class, 'inventory_item_id');
    }
}
