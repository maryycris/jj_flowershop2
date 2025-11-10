<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_name',
        'contact',
        'address',
        'order_date_received',
        'status',
        'total_amount',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_order_product')
            ->withPivot('quantity', 'received', 'unit_price', 'subtotal');
    }
} 