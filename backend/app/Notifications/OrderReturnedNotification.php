<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderReturnedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $notificationType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $notificationType = 'customer')
    {
        $this->order = $order;
        $this->notificationType = $notificationType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        if ($this->notificationType === 'customer') {
            return $this->getCustomerMailMessage();
        } elseif ($this->notificationType === 'admin') {
            return $this->getAdminMailMessage();
        } else {
            return $this->getClerkMailMessage();
        }
    }

    /**
     * Get the database representation of the notification.
     */
    public function toArray($notifiable)
    {
        if ($this->notificationType === 'customer') {
            return [
                'type' => 'order_returned',
                'title' => "Order #{$this->order->id} Returned",
                'message' => "Your order #{$this->order->id} has been returned. Reason: {$this->order->return_reason}. Please contact support for assistance.",
                'order_id' => $this->order->id,
                'icon' => 'fas fa-undo',
                'color' => 'warning',
                'action_url' => route('customer.orders.index', ['status' => 'returned'])
            ];
        } elseif ($this->notificationType === 'admin') {
            return [
                'type' => 'admin_order_returned',
                'title' => "URGENT: Order #{$this->order->id} Returned",
                'message' => "Order #{$this->order->id} has been returned by driver {$this->order->returnedByDriver->name}. Reason: {$this->order->return_reason}. Action required.",
                'order_id' => $this->order->id,
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'danger',
                'action_url' => route('admin.orders.show', $this->order->id)
            ];
        } elseif ($this->notificationType === 'customer_rejected') {
            return [
                'type' => 'return_rejected',
                'title' => "Return Rejected - Order #{$this->order->id}",
                'message' => "Your return request for order #{$this->order->id} has been rejected. Please contact support for more information.",
                'order_id' => $this->order->id,
                'icon' => 'fas fa-times-circle',
                'color' => 'danger',
                'action_url' => route('customer.orders.show', $this->order->id)
            ];
        } elseif ($this->notificationType === 'customer_resolved') {
            return [
                'type' => 'return_resolved',
                'title' => "Return Resolved - Order #{$this->order->id}",
                'message' => "Your return request for order #{$this->order->id} has been resolved. Thank you for your patience.",
                'order_id' => $this->order->id,
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
                'action_url' => route('customer.orders.show', $this->order->id)
            ];
        } elseif ($this->notificationType === 'customer_refunded') {
            return [
                'type' => 'refund_processed',
                'title' => "Refund Processed - Order #{$this->order->id}",
                'message' => "Your refund for order #{$this->order->id} has been processed. Amount: ₱" . number_format($this->order->refund_amount ?? $this->order->total_price, 2),
                'order_id' => $this->order->id,
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'success',
                'action_url' => route('customer.orders.show', $this->order->id)
            ];
        } else {
            return [
                'type' => 'clerk_order_returned',
                'title' => "Order #{$this->order->id} Returned",
                'message' => "Order #{$this->order->id} has been returned. Reason: {$this->order->return_reason}. Review required.",
                'order_id' => $this->order->id,
                'icon' => 'fas fa-undo',
                'color' => 'warning',
                'action_url' => route('clerk.orders.show', $this->order->id)
            ];
        }
    }

    private function getCustomerMailMessage()
    {
        return (new MailMessage)
            ->subject("Order #{$this->order->id} Returned - J'J Flower Shop")
            ->greeting('Hello ' . $this->order->user->name . ',')
            ->line("We regret to inform you that your order #{$this->order->id} has been returned.")
            ->line("**Return Reason:** {$this->order->return_reason}")
            ->line("**Order Total:** ₱" . number_format($this->order->total_price, 2))
            ->line('Please contact our support team to arrange re-delivery or discuss refund options.')
            ->action('Contact Support', route('customer.orders.index'))
            ->line('Thank you for your understanding.');
    }

    private function getAdminMailMessage()
    {
        return (new MailMessage)
            ->subject("URGENT: Order #{$this->order->id} Returned - Action Required")
            ->greeting('Admin Alert')
            ->line("Order #{$this->order->id} has been returned by driver {$this->order->returnedByDriver->name}.")
            ->line("**Return Reason:** {$this->order->return_reason}")
            ->line("**Customer:** {$this->order->user->name}")
            ->line("**Order Total:** ₱" . number_format($this->order->total_price, 2))
            ->line('**Action Required:** Review the return and contact customer for resolution.')
            ->action('Review Order', route('admin.orders.show', $this->order->id));
    }

    private function getClerkMailMessage()
    {
        return (new MailMessage)
            ->subject("Order #{$this->order->id} Returned - Review Required")
            ->greeting('Clerk Notification')
            ->line("Order #{$this->order->id} has been returned and requires your review.")
            ->line("**Return Reason:** {$this->order->return_reason}")
            ->line("**Customer:** {$this->order->user->name}")
            ->line("**Driver:** {$this->order->returnedByDriver->name}")
            ->action('Review Order', route('clerk.orders.show', $this->order->id));
    }
}