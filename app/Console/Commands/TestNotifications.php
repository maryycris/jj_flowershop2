<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Order;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderPlacedNotification;

class TestNotifications extends Command
{
    protected $signature = 'notifications:test';
    protected $description = 'Create test notifications for all user types';

    public function handle()
    {
        // Get users
        $admin = User::where('role', 'admin')->first();
        $clerk = User::where('role', 'clerk')->first();
        $customer = User::where('role', 'customer')->first();

        if (!$admin || !$clerk || !$customer) {
            $this->error('Missing users: Admin=' . ($admin ? 'OK' : 'MISSING') . ', Clerk=' . ($clerk ? 'OK' : 'MISSING') . ', Customer=' . ($customer ? 'OK' : 'MISSING'));
            return;
        }

        // Create a test order
        $order = Order::create([
            'user_id' => $customer->id,
            'total_price' => 500.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'type' => 'online'
        ]);

        // Send notifications
        $admin->notify(new NewOrderNotification($order));
        $clerk->notify(new NewOrderNotification($order));
        $customer->notify(new OrderPlacedNotification($order));

        $this->info('Test notifications created:');
        $this->info("- Admin ({$admin->name}): " . $admin->notifications()->count() . " notifications");
        $this->info("- Clerk ({$clerk->name}): " . $clerk->notifications()->count() . " notifications");
        $this->info("- Customer ({$customer->name}): " . $customer->notifications()->count() . " notifications");
    }
}
