<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductComposition extends Model
{
    protected $fillable = [
        'product_id',
        'component_id',
        'component_name',
        'quantity',
        'unit',
        'description'
    ];

    /**
     * Get the product that owns the composition.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
