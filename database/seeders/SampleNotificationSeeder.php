<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Notifications\GeneralNotification;

class SampleNotificationSeeder extends Seeder
{
    public function run()
    {
        // Get or create a customer user
        $customer = User::where('role', 'customer')->first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Test Customer',
                'email' => 'customer@test.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'is_verified' => true,
                'contact_number' => '09123456789'
            ]);
        }

        // Clear existing notifications for this customer
        $customer->notifications()->delete();

        // Create sample notifications
        $notifications = [
            [
                'type' => 'order_status',
                'title' => 'Order Approved',
                'message' => 'Great news! Your order #123 has been approved and is being prepared.',
                'action_url' => route('customer.orders.index'),
                'icon' => 'fas fa-check-circle',
                'color' => 'success'
            ],
            [
                'type' => 'payment_verification',
                'title' => 'Payment Verification Required',
                'message' => 'Please complete payment for Order #123 using GCash. Amount: ₱1,500.00',
                'action_url' => route('customer.orders.index'),
                'icon' => 'fas fa-credit-card',
                'color' => 'warning'
            ],
            [
                'type' => 'system',
                'title' => 'Welcome to JJ Flower Shop!',
                'message' => 'Thank you for joining us. Explore our beautiful collection of flowers.',
                'action_url' => route('customer.products.index'),
                'icon' => 'fas fa-heart',
                'color' => 'success'
            ],
            [
                'type' => 'promotion',
                'title' => 'Special Offer Available!',
                'message' => 'Get 20% off on all bouquets this week. Use code SPRING20 at checkout.',
                'action_url' => route('customer.products.index'),
                'icon' => 'fas fa-gift',
                'color' => 'warning'
            ],
            [
                'type' => 'order_status',
                'title' => 'Order On Delivery',
                'message' => 'Your order #123 is out for delivery. Track your order for real-time updates.',
                'action_url' => route('customer.orders.index'),
                'icon' => 'fas fa-truck',
                'color' => 'primary'
            ],
            [
                'type' => 'order_status',
                'title' => 'Order Completed',
                'message' => 'Your order #123 has been delivered successfully. Thank you for choosing us!',
                'action_url' => route('customer.orders.index'),
                'icon' => 'fas fa-check-double',
                'color' => 'success'
            ]
        ];

        foreach ($notifications as $notificationData) {
            $customer->notify(new GeneralNotification($notificationData));
        }

        // Sample notifications created for customer: ' . $customer->name
    }
}
