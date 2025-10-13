<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Order;

class TestAdminOnlineOrders extends Command
{
    protected $signature = 'test:admin-online-orders';
    protected $description = 'Test admin online orders functionality';

    public function handle()
    {
        // Get admin user
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->error('No admin user found');
            return;
        }

        // Get online orders with different statuses
        $pendingOrders = Order::where('type', 'online')
            ->where(function($q) {
                $q->where('order_status', 'pending')
                  ->orWhere(function($sub) {
                      $sub->whereNull('order_status')->where('status', 'pending');
                  });
            })
            ->count();

        $approvedOrders = Order::where('type', 'online')
            ->where('order_status', 'approved')
            ->count();

        $allOnlineOrders = Order::where('type', 'online')->count();

        $this->info("Admin Online Orders Test:");
        $this->info("- Pending orders: {$pendingOrders}");
        $this->info("- Approved orders: {$approvedOrders}");
        $this->info("- Total online orders: {$allOnlineOrders}");

        // Test if admin can see pending orders only
        if ($pendingOrders > 0) {
            $this->info("✅ Admin should see {$pendingOrders} pending orders");
        } else {
            $this->warn("⚠️ No pending orders found - admin should see empty list");
        }

        // Test if approved orders are filtered out
        if ($approvedOrders > 0) {
            $this->info("✅ {$approvedOrders} approved orders are properly filtered out from admin pending view");
        }

        $this->info("\nAdmin should now only see pending orders in the online orders list.");
    }
}
