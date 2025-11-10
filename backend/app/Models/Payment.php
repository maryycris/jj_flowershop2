<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'payment_number',
        'invoice_id',
        'order_id',
        'mode_of_payment',
        'amount',
        'payment_date',
        'memo',
        'processed_by',
        'status'
    ];
    
    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];
    
    /**
     * Get the invoice that owns the payment
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    
    /**
     * Get the user who processed the payment
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the order associated with payment
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get formatted payment date
     */
    public function getFormattedPaymentDateAttribute(): string
    {
        return $this->payment_date->format('M d, Y');
    }

    /**
     * Scope: Get completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Get payments by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }
}