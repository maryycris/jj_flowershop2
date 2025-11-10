<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\LoyaltyService;

class FixLoyaltyStamps extends Command
{
    protected $signature = 'loyalty:fix-stamps {user_id?}';
    protected $description = 'Fix loyalty stamps for completed orders';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $loyaltyService = new LoyaltyService();
        
        // Get completed orders
        $query = Order::whereIn('order_status', ['delivered', 'completed'])
                     ->whereIn('payment_status', ['paid', 'validated', 'paid_cod']);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $orders = $query->with(['products', 'customBouquets'])->get();
        
        $this->info("Found {$orders->count()} completed orders");
        
        $stampsIssued = 0;
        foreach ($orders as $order) {
            try {
                $loyaltyService->issueStampIfEligible($order);
                $stampsIssued++;
                $this->info("Issued stamp for order #{$order->id} (User: {$order->user_id})");
            } catch (\Exception $e) {
                $this->error("Failed to issue stamp for order #{$order->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Issued {$stampsIssued} loyalty stamps");
    }
}