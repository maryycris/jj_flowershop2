<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class CheckOrders extends Command
{
    protected $signature = 'check:orders';
    protected $description = 'Check recent orders and their status';

    public function handle()
    {
        $this->info('Recent Orders:');
        
        $orders = Order::with('user')->latest()->take(10)->get();
        
        foreach ($orders as $order) {
            $this->info("Order #{$order->id} - Status: {$order->order_status} - Driver: " . ($order->assigned_driver_id ?? 'None') . " - User: " . ($order->user->name ?? 'N/A'));
        }
    }
}
