<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Driver;
use App\Services\OrderStatusService;

class TestAssignDriver extends Command
{
    protected $signature = 'test:assign-driver {order_id} {driver_id}';
    protected $description = 'Test assigning a driver to an order';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        $driverId = $this->argument('driver_id');
        
        $order = Order::find($orderId);
        if (!$order) {
            $this->error("Order #{$orderId} not found");
            return;
        }
        
        $driver = Driver::find($driverId);
        if (!$driver) {
            $this->error("Driver #{$driverId} not found");
            return;
        }
        
        $this->info("Assigning Driver #{$driverId} ({$driver->user->name}) to Order #{$orderId}");
        
        $orderStatusService = new OrderStatusService();
        
        if ($orderStatusService->assignDriver($order, $driver->user_id, 1)) {
            $this->info("✅ Driver assigned successfully!");
            
            // Refresh the order to see updated status
            $order->refresh();
            $this->info("Order status: {$order->order_status}");
            $this->info("Assigned driver: {$order->assigned_driver_id}");
        } else {
            $this->error("❌ Failed to assign driver");
        }
    }
}
