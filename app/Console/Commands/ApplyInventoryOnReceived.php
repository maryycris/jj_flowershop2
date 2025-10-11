<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class ApplyInventoryOnReceived extends Command
{
    protected $signature = 'inventory:apply-received {orderId}';
    protected $description = 'Apply inventory consumption for an already completed/received order';

    public function handle(): int
    {
        $orderId = (int) $this->argument('orderId');
        $order = Order::with('products')->find($orderId);
        if (!$order) {
            $this->error("Order {$orderId} not found");
            return Command::FAILURE;
        }
        try {
            $service = new \App\Services\InventoryService();
            $ok = $service->updateInventoryOnReceived($order);
            if ($ok) {
                $this->info("Inventory updated for order {$orderId}.");
                return Command::SUCCESS;
            }
            $this->error('Service returned false. See logs for details.');
            return Command::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}


