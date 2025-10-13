<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderStatusNotification;
use App\Notifications\ProductApprovalNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\NewChatMessageNotification;

class NotificationService
{
    /**
     * Send order status notification to customer
     */
    public function sendOrderStatusNotification(Order $order, string $status, string $message = null)
    {
        $customer = $order->user;
        
        $notificationData = [
            'type' => 'order_status',
            'title' => $this->getOrderStatusTitle($status),
            'message' => $message ?? $this->getOrderStatusMessage($order, $status),
            'order_id' => $order->id,
            'action_url' => route('customer.orders.show', $order->id),
            'icon' => $this->getOrderStatusIcon($status),
            'color' => $this->getOrderStatusColor($status)
        ];

        $customer->notify(new OrderStatusNotification($notificationData));
    }


    /**
     * Send product approval notification to admin
     */
    public function sendProductApprovalNotification($product, User $clerk, string $action = 'added')
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $notificationData = [
                'type' => 'product_approval',
                'title' => 'Product ' . ucfirst($action),
                'message' => "Clerk {$clerk->name} {$action} a new product: {$product->name}",
                'product_id' => $product->id,
                'clerk_id' => $clerk->id,
                'action_url' => route('admin.products.show', $product->id),
                'icon' => 'fas fa-box',
                'color' => 'primary'
            ];

            $admin->notify(new ProductApprovalNotification($notificationData));
        }
    }

    /**
     * Send low stock notification to admin
     */
    public function sendLowStockNotification(Product $product)
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $notificationData = [
                'type' => 'low_stock',
                'title' => 'Low Stock Alert',
                'message' => "{$product->name} is running low on stock. Current: {$product->stock}, Minimum: {$product->reorder_min}",
                'product_id' => $product->id,
                'action_url' => route('admin.inventory.index'),
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'warning'
            ];

            $admin->notify(new LowStockNotification($notificationData));
        }
    }

    /**
     * Send chat message notification
     */
    public function sendChatMessageNotification(User $recipient, User $sender, string $message)
    {
        $notificationData = [
            'type' => 'chat_message',
            'title' => 'New Message from ' . $sender->name,
            'message' => substr($message, 0, 100) . (strlen($message) > 100 ? '...' : ''),
            'sender_id' => $sender->id,
            'action_url' => route('customer.chat.index'),
            'icon' => 'fas fa-comment',
            'color' => 'info'
        ];

        $recipient->notify(new NewChatMessageNotification($notificationData));
    }

    /**
     * Get order status title
     */
    private function getOrderStatusTitle(string $status): string
    {
        return match($status) {
            'pending' => 'Order Pending',
            'approved' => 'Order Approved',
            'processing' => 'Order Processing',
            'on_delivery' => 'Order On Delivery',
            'completed' => 'Order Completed',
            'cancelled' => 'Order Cancelled',
            default => 'Order Update'
        };
    }

    /**
     * Get order status message
     */
    private function getOrderStatusMessage(Order $order, string $status): string
    {
        return match($status) {
            'pending' => "Your order #{$order->id} is pending approval. We'll review it soon.",
            'approved' => "Great news! Your order #{$order->id} has been approved and is being prepared.",
            'processing' => "Your order #{$order->id} is being prepared for delivery.",
            'on_delivery' => "Your order #{$order->id} is out for delivery. Track your order for real-time updates.",
            'completed' => "Your order #{$order->id} has been delivered successfully. Thank you for choosing us!",
            'cancelled' => "Your order #{$order->id} has been cancelled. Please contact support if you have questions.",
            default => "Your order #{$order->id} status has been updated."
        };
    }

    /**
     * Get order status icon
     */
    private function getOrderStatusIcon(string $status): string
    {
        return match($status) {
            'pending' => 'fas fa-clock',
            'approved' => 'fas fa-check-circle',
            'processing' => 'fas fa-cog',
            'on_delivery' => 'fas fa-truck',
            'completed' => 'fas fa-check-double',
            'cancelled' => 'fas fa-times-circle',
            default => 'fas fa-bell'
        };
    }

    /**
     * Get order status color
     */
    private function getOrderStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'approved' => 'success',
            'processing' => 'info',
            'on_delivery' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

}
