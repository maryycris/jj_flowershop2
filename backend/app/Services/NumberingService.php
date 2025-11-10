<?php

namespace App\Services;

use App\Models\NumberingCounter;
use Illuminate\Support\Facades\DB;

class NumberingService
{
    /**
     * Generate the next number for a given type
     */
    public function generateNextNumber(string $type): string
    {
        return DB::transaction(function () use ($type) {
            $counter = NumberingCounter::where('type', $type)->lockForUpdate()->first();
            
            if (!$counter) {
                throw new \Exception("Numbering counter for type '{$type}' not found");
            }
            
            $counter->current_number++;
            $counter->save();
            
            return $counter->prefix . str_pad($counter->current_number, $counter->padding_length, '0', STR_PAD_LEFT);
        });
    }
    
    /**
     * Generate Sales Order number ensuring uniqueness even if historical data exists.
     * This method synchronizes the counter with the highest existing SO number
     * and retries if a collision is detected.
     */
    public function generateSalesOrderNumber(): string
    {
        return DB::transaction(function () {
            $counter = NumberingCounter::where('type', 'sales_order')->lockForUpdate()->first();

            if (!$counter) {
                $counter = NumberingCounter::create([
                    'type' => 'sales_order',
                    'prefix' => 'SO-',
                    'current_number' => 0,
                    'padding_length' => 5,
                ]);
            }

            // Sync counter with highest existing numeric suffix if the DB already has records
            $latestSo = \App\Models\SalesOrder::orderByDesc('id')->value('so_number');
            if ($latestSo) {
                if (preg_match('/(\d+)$/', $latestSo, $m)) {
                    $highestExisting = (int) $m[1];
                    if ($highestExisting > $counter->current_number) {
                        $counter->current_number = $highestExisting;
                    }
                }
            }

            $attempts = 0;
            do {
                $attempts++;
                $counter->current_number++;
                $next = $counter->prefix . str_pad($counter->current_number, $counter->padding_length, '0', STR_PAD_LEFT);
                $exists = \App\Models\SalesOrder::where('so_number', $next)->exists();
            } while ($exists && $attempts < 10);

            // Persist updated counter
            $counter->save();

            if ($exists) {
                // After several attempts, give a descriptive error instead of generic SQL duplicate
                throw new \RuntimeException('Unable to generate a unique Sales Order number after several attempts.');
            }

            return $next;
        });
    }
    
    /**
     * Generate Invoice number
     */
    public function generateInvoiceNumber(): string
    {
        return $this->generateNextNumber('invoice');
    }
    
    /**
     * Generate Order number
     */
    public function generateOrderNumber(): string
    {
        return $this->generateNextNumber('order');
    }
    
    /**
     * Get current number for a type (without incrementing)
     */
    public function getCurrentNumber(string $type): string
    {
        $counter = NumberingCounter::where('type', $type)->first();
        
        if (!$counter) {
            throw new \Exception("Numbering counter for type '{$type}' not found");
        }
        
        return $counter->prefix . str_pad($counter->current_number, $counter->padding_length, '0', STR_PAD_LEFT);
    }
}
