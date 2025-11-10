<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Notifications\GeneralNotification;

class SampleRichNotificationsSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(12);
        if (!$user) {
            $this->command->error('User with ID 12 not found');
            return;
        }

        // Clear existing notifications
        $user->notifications()->delete();

        // Create sample notifications with rich data
        $user->notify(new GeneralNotification([
            'title' => 'Payment Verified',
            'message' => 'Payment for Order #165 has been verified and approved. Your order is being processed.',
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'action_url' => '/customer/orders'
        ]));

        $user->notify(new GeneralNotification([
            'title' => 'Welcome to JJ Flower Shop!',
            'message' => 'Thank you for joining us. Explore our beautiful collection of flowers and create memorable moments.',
            'icon' => 'fas fa-heart',
            'color' => 'success',
            'action_url' => '/customer/products'
        ]));

        $user->notify(new GeneralNotification([
            'title' => 'Order Completed',
            'message' => 'Your order #164 has been delivered successfully. Thank you for choosing us!',
            'icon' => 'fas fa-check',
            'color' => 'success',
            'action_url' => '/customer/orders'
        ]));

        $user->notify(new GeneralNotification([
            'title' => 'Payment Verification Required',
            'message' => 'Please complete payment for Order #164 using GCash. Amount: P430.00',
            'icon' => 'fas fa-credit-card',
            'color' => 'warning',
            'action_url' => '/customer/orders'
        ]));

        $this->command->info('Created 4 sample notifications with rich data for user ID 12');
    }
}
