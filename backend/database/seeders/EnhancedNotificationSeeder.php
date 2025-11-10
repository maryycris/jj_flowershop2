<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Services\NotificationService;
use App\Notifications\GeneralNotification;

class EnhancedNotificationSeeder extends Seeder
{
    protected $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function run()
    {
        // Get sample users
        $customers = User::where('role', 'customer')->take(3)->get();
        $admins = User::where('role', 'admin')->take(2)->get();
        $clerks = User::where('role', 'clerk')->take(2)->get();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Creating sample customers...');
            $customers = collect([
                User::create([
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                    'is_verified' => true,
                    'contact_number' => '09123456789'
                ])
            ]);
        }

        if ($admins->isEmpty()) {
            $this->command->warn('No admins found. Creating sample admin...');
            $admins = collect([
                User::create([
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'admin',
                    'is_verified' => true,
                    'contact_number' => '09123456788'
                ])
            ]);
        }

        // Create sample orders for notifications
        $orders = Order::take(3)->get();
        if ($orders->isEmpty()) {
            $this->command->warn('No orders found. Creating sample orders...');
            $orders = collect([
                Order::create([
                    'user_id' => $customers->first()->id,
                    'total_price' => 1500.00,
                    'status' => 'pending',
                    'payment_method' => 'cod',
                    'payment_status' => 'unpaid'
                ])
            ]);
        }

        // Create various types of notifications
        $this->createOrderNotifications($customers, $orders);
        $this->createPaymentNotifications($customers, $orders);
        $this->createSystemNotifications($customers);
        $this->createAdminNotifications($admins);
        $this->createClerkNotifications($clerks);

        // Enhanced notifications created successfully!
    }

    private function createOrderNotifications($customers, $orders)
    {
        foreach ($customers as $customer) {
            foreach ($orders as $order) {
                // Order status notifications
                $statuses = ['pending', 'approved', 'processing', 'on_delivery', 'completed'];
                foreach ($statuses as $status) {
                    $notificationData = [
                        'type' => 'order_status',
                        'title' => $this->getOrderStatusTitle($status),
                        'message' => $this->getOrderStatusMessage($order, $status),
                        'order_id' => $order->id,
                        'action_url' => route('customer.orders.show', $order->id),
                        'icon' => $this->getOrderStatusIcon($status),
                        'color' => $this->getOrderStatusColor($status)
                    ];

                    $customer->notify(new GeneralNotification($notificationData));
                }
            }
        }
    }

    private function createPaymentNotifications($customers, $orders)
    {
        foreach ($customers as $customer) {
            foreach ($orders as $order) {
                // Payment verification notifications
                $paymentTypes = ['created', 'verified', 'rejected'];
                foreach ($paymentTypes as $type) {
                    $notificationData = [
                        'type' => 'payment_verification',
                        'title' => $this->getPaymentVerificationTitle($type),
                        'message' => $this->getPaymentVerificationMessage($order, $type),
                        'order_id' => $order->id,
                        'action_url' => $type === 'created' 
                            ? route('customer.payment-verification.show', $order)
                            : route('customer.orders.show', $order),
                        'icon' => 'fas fa-credit-card',
                        'color' => $this->getPaymentVerificationColor($type)
                    ];

                    $customer->notify(new GeneralNotification($notificationData));
                }
            }
        }
    }

    private function createSystemNotifications($customers)
    {
        $systemNotifications = [
            [
                'type' => 'system',
                'title' => 'Welcome to JJ Flower Shop!',
                'message' => 'Thank you for joining us. Explore our beautiful collection of flowers and create memorable moments.',
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
                'type' => 'maintenance',
                'title' => 'Scheduled Maintenance',
                'message' => 'Our system will undergo maintenance tonight from 11 PM to 1 AM. We apologize for any inconvenience.',
                'action_url' => null,
                'icon' => 'fas fa-tools',
                'color' => 'info'
            ]
        ];

        foreach ($customers as $customer) {
            foreach ($systemNotifications as $notificationData) {
                $customer->notify(new GeneralNotification($notificationData));
            }
        }
    }

    private function createAdminNotifications($admins)
    {
        $adminNotifications = [
            [
                'type' => 'product_approval',
                'title' => 'New Product Pending Approval',
                'message' => 'Clerk John submitted a new product "Red Roses Bouquet" for approval.',
                'action_url' => route('admin.products.index'),
                'icon' => 'fas fa-box',
                'color' => 'primary'
            ],
            [
                'type' => 'low_stock',
                'title' => 'Low Stock Alert',
                'message' => 'White Lilies are running low on stock. Current: 5, Minimum: 10',
                'action_url' => route('admin.inventory.index'),
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'warning'
            ],
            [
                'type' => 'payment_verification',
                'title' => 'Payment Verification Required',
                'message' => 'Order #123 needs payment verification for GCash payment.',
                'action_url' => route('admin.payment-verifications.index'),
                'icon' => 'fas fa-credit-card',
                'color' => 'info'
            ]
        ];

        foreach ($admins as $admin) {
            foreach ($adminNotifications as $notificationData) {
                $admin->notify(new GeneralNotification($notificationData));
            }
        }
    }

    private function createClerkNotifications($clerks)
    {
        $clerkNotifications = [
            [
                'type' => 'order',
                'title' => 'New Order Received',
                'message' => 'A new order #456 has been placed and requires your attention.',
                'action_url' => route('clerk.orders.index'),
                'icon' => 'fas fa-shopping-cart',
                'color' => 'success'
            ],
            [
                'type' => 'inventory',
                'title' => 'Inventory Update Required',
                'message' => 'Please update the inventory for recently delivered products.',
                'action_url' => route('clerk.inventory.index'),
                'icon' => 'fas fa-warehouse',
                'color' => 'info'
            ]
        ];

        foreach ($clerks as $clerk) {
            foreach ($clerkNotifications as $notificationData) {
                $clerk->notify(new GeneralNotification($notificationData));
            }
        }
    }

    // Helper methods
    private function getOrderStatusTitle($status)
    {
        return match($status) {
            'pending' => 'Order Pending',
            'approved' => 'Order Approved',
            'processing' => 'Order Processing',
            'on_delivery' => 'Order On Delivery',
            'completed' => 'Order Completed',
            default => 'Order Update'
        };
    }

    private function getOrderStatusMessage($order, $status)
    {
        return match($status) {
            'pending' => "Your order #{$order->id} is pending approval. We'll review it soon.",
            'approved' => "Great news! Your order #{$order->id} has been approved and is being prepared.",
            'processing' => "Your order #{$order->id} is being prepared for delivery.",
            'on_delivery' => "Your order #{$order->id} is out for delivery. Track your order for real-time updates.",
            'completed' => "Your order #{$order->id} has been delivered successfully. Thank you for choosing us!",
            default => "Your order #{$order->id} status has been updated."
        };
    }

    private function getOrderStatusIcon($status)
    {
        return match($status) {
            'pending' => 'fas fa-clock',
            'approved' => 'fas fa-check-circle',
            'processing' => 'fas fa-cog',
            'on_delivery' => 'fas fa-truck',
            'completed' => 'fas fa-check-double',
            default => 'fas fa-bell'
        };
    }

    private function getOrderStatusColor($status)
    {
        return match($status) {
            'pending' => 'warning',
            'approved' => 'success',
            'processing' => 'info',
            'on_delivery' => 'primary',
            'completed' => 'success',
            default => 'secondary'
        };
    }

    private function getPaymentVerificationTitle($type)
    {
        return match($type) {
            'created' => 'Payment Verification Required',
            'verified' => 'Payment Verified',
            'rejected' => 'Payment Rejected',
            default => 'Payment Update'
        };
    }

    private function getPaymentVerificationMessage($order, $type)
    {
        $amount = number_format($order->total_price, 2);
        return match($type) {
            'created' => "Please complete payment for Order #{$order->id} using GCash. Amount: ₱{$amount}",
            'verified' => "Payment for Order #{$order->id} has been verified and approved. Your order is being processed.",
            'rejected' => "Payment for Order #{$order->id} was rejected. Please check the details and try again.",
            default => "Payment verification for Order #{$order->id} has been updated."
        };
    }

    private function getPaymentVerificationColor($type)
    {
        return match($type) {
            'created' => 'warning',
            'verified' => 'success',
            'rejected' => 'danger',
            default => 'info'
        };
    }
}
