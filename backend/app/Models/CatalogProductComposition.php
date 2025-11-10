<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogProductComposition extends Model
{
    protected $fillable = [
        'catalog_product_id',
        'component_id',
        'component_name',
        'quantity',
        'unit',
        'description'
    ];

    /**
     * Get the catalog product that owns the composition.
     */
    public function catalogProduct()
    {
        return $this->belongsTo(CatalogProduct::class);
    }

    /**
     * Get the component product.
     */
    public function componentProduct()
    {
        return $this->belongsTo(Product::class, 'component_id');
    }
}
