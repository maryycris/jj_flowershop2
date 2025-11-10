<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Order;
use App\Models\Delivery;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Listen for order creation
        Order::created(function ($order) {
            \Log::info('Order created event fired', ['order_id' => $order->id]);
            
            // Create notification for admin
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $admin->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\Notifications\NewOrderNotification',
                    'data' => json_encode([
                        'message' => 'New order #' . $order->id . ' has been placed by ' . $order->user->name,
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'total_price' => $order->total_price,
                    ]),
                ]);
            }
            
            // Create notification for customer
            $order->user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\OrderPlacedNotification',
                'data' => json_encode([
                    'message' => 'Your order #' . $order->id . ' has been placed successfully!',
                    'order_id' => $order->id,
                    'total_price' => $order->total_price,
                ]),
            ]);
        });

        // Listen for delivery creation
        Delivery::created(function ($delivery) {
            \Log::info('Delivery created event fired', ['delivery_id' => $delivery->id]);
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
} 