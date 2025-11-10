<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'action',
        'status',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the product that was modified
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made the change
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted description of the change
     */
    public function getFormattedDescriptionAttribute()
    {
        $productName = $this->product ? $this->product->name : 'Unknown Product';
        $userName = $this->user ? $this->user->name : 'Unknown User';
        
        switch ($this->action) {
            case 'edit':
                return "{$userName} edited {$productName}";
            case 'delete':
                return "{$userName} marked {$productName} for deletion";
            case 'restore':
                return "{$userName} unmarked {$productName} for deletion";
            case 'create':
                return "{$userName} created {$productName}";
            default:
                return "{$userName} performed {$this->action} on {$productName}";
        }
    }

    /**
     * Get the changes summary
     */
    public function getChangesSummaryAttribute()
    {
        if ($this->action === 'edit' && $this->old_values && $this->new_values) {
            $changes = [];
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue != $newValue) {
                    $changes[] = ucfirst(str_replace('_', ' ', $key)) . ": {$oldValue} → {$newValue}";
                }
            }
            return implode(', ', $changes);
        }
        return null;
    }
}