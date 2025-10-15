<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'movement_type',
        'movement_number',
        'quantity',
        'unit_cost',
        'notes',
        'user_id',
        'reference_type',
        'reference_id'
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Generate unique movement number
     */
    public static function generateMovementNumber($type = 'OUT')
    {
        $prefix = $type;
        $lastMovement = self::where('movement_type', $type)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastMovement ? 
            (int)substr($lastMovement->movement_number, -4) + 1 : 1;
        
        return $prefix . ' / ' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the order that owns the movement
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product that was moved
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made the movement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted movement description
     */
    public function getFormattedDescriptionAttribute()
    {
        $productName = $this->product ? $this->product->name : 'Unknown Product';
        $userName = $this->user ? $this->user->name : 'Unknown User';
        
        return "{$userName} moved {$this->quantity} units of {$productName} ({$this->movement_type})";
    }

    /**
     * Scope for OUT movements
     */
    public function scopeOut($query)
    {
        return $query->where('movement_type', 'OUT');
    }

    /**
     * Scope for IN movements
     */
    public function scopeIn($query)
    {
        return $query->where('movement_type', 'IN');
    }

    /**
     * Scope for specific product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
