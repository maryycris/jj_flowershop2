<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Driver;
use App\Models\Order;

class TestDriverAssignment extends Command
{
    protected $signature = 'test:driver-assignment';
    protected $description = 'Test driver assignment functionality';

    public function handle()
    {
        $this->info('Testing Driver Assignment Process...');
        
        // Check if there are any drivers
        $drivers = Driver::with('user')->get();
        $this->info("Total drivers found: " . $drivers->count());
        
        foreach ($drivers as $driver) {
            $this->info("- Driver ID: {$driver->id}, User ID: {$driver->user_id}, Name: " . ($driver->user->name ?? 'N/A') . ", Active: " . ($driver->is_active ? 'Yes' : 'No'));
        }
        
        // Check for orders with assigned drivers
        $assignedOrders = Order::whereNotNull('assigned_driver_id')->get();
        $this->info("\nOrders with assigned drivers: " . $assignedOrders->count());
        
        foreach ($assignedOrders as $order) {
            $this->info("- Order #{$order->id}, Status: {$order->order_status}, Assigned Driver: {$order->assigned_driver_id}");
        }
        
        // Check for orders with 'assigned' status
        $assignedStatusOrders = Order::where('order_status', 'assigned')->get();
        $this->info("\nOrders with 'assigned' status: " . $assignedStatusOrders->count());
        
        foreach ($assignedStatusOrders as $order) {
            $this->info("- Order #{$order->id}, Assigned Driver: {$order->assigned_driver_id}");
        }
        
        // Check for orders with 'on_delivery' status
        $onDeliveryOrders = Order::where('order_status', 'on_delivery')->get();
        $this->info("\nOrders with 'on_delivery' status: " . $onDeliveryOrders->count());
        
        foreach ($onDeliveryOrders as $order) {
            $this->info("- Order #{$order->id}, Assigned Driver: {$order->assigned_driver_id}");
        }
        
        $this->info("\nDriver Assignment Test Complete!");
    }
}
