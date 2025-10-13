<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\GeneralNotification;

class FixUserNotifications extends Command
{
    protected $signature = 'notifications:fix-user {user_id=12}';
    protected $description = 'Fix notifications for a specific user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return;
        }

        $this->info("Fixing notifications for user: {$user->name} (ID: {$user->id})");
        $this->info("Current notifications count: " . $user->notifications()->count());

        // Clear existing notifications
        $user->notifications()->delete();
        $this->info("Cleared existing notifications");

        // Create new notifications
        $notifications = [
            [
                'type' => 'order_status',
                'title' => 'Order Completed',
                'message' => 'Your order #123 has been delivered successfully. Thank you for choosing us!',
                'action_url' => 'http://localhost:8000/customer/orders',
                'icon' => 'fas fa-check-double',
                'color' => 'success'
            ],
            [
                'type' => 'payment_verification',
                'title' => 'Payment Verification Required',
                'message' => 'Please complete payment for Order #123 using GCash. Amount: ₱1,500.00',
                'action_url' => 'http://localhost:8000/customer/orders',
                'icon' => 'fas fa-credit-card',
                'color' => 'warning'
            ],
            [
                'type' => 'system',
                'title' => 'Welcome to JJ Flower Shop!',
                'message' => 'Thank you for joining us. Explore our beautiful collection of flowers.',
                'action_url' => 'http://localhost:8000/customer/products',
                'icon' => 'fas fa-heart',
                'color' => 'success'
            ],
            [
                'type' => 'promotion',
                'title' => 'Special Offer Available!',
                'message' => 'Get 20% off on all bouquets this week. Use code SPRING20 at checkout.',
                'action_url' => 'http://localhost:8000/customer/products',
                'icon' => 'fas fa-gift',
                'color' => 'warning'
            ]
        ];

        foreach ($notifications as $notificationData) {
            $user->notify(new GeneralNotification($notificationData));
        }

        $this->info("Created " . count($notifications) . " notifications");
        $this->info("New notifications count: " . $user->notifications()->count());
        $this->info("✅ Notifications fixed for user {$user->name}!");
    }
}