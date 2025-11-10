<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Get a clerk user
        $clerk = User::where('role', 'clerk')->first();
        
        if (!$clerk) {
            // Create a clerk user if none exists
            $clerk = User::create([
                'name' => 'Test Clerk',
                'email' => 'clerk@test.com',
                'password' => bcrypt('password'),
                'role' => 'clerk',
                'is_verified' => true,
                'contact_number' => '1234567890'
            ]);
        }

        // Create sample notifications
        $notifications = [
            [
                'title' => 'New Order Received',
                'message' => 'A new order #12345 has been placed and requires your attention.',
                'type' => 'order'
            ],
            [
                'title' => 'Low Stock Alert',
                'message' => 'Red roses are running low on stock. Please consider reordering.',
                'type' => 'inventory'
            ],
            [
                'title' => 'Event Booking Update',
                'message' => 'Event booking #EVT001 status has been updated to "Confirmed".',
                'type' => 'event'
            ],
            [
                'title' => 'System Maintenance',
                'message' => 'Scheduled maintenance will occur tonight from 11 PM to 1 AM.',
                'type' => 'system'
            ],
            [
                'title' => 'Payment Received',
                'message' => 'Payment for order #12345 has been successfully processed.',
                'type' => 'payment'
            ]
        ];

        foreach ($notifications as $notificationData) {
            $clerk->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\GenericNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $clerk->id,
                'data' => json_encode($notificationData),
                'read_at' => null,
                'created_at' => now()->subDays(rand(1, 7)),
                'updated_at' => now()->subDays(rand(1, 7))
            ]);
        }

        $this->command->info('Sample notifications created for clerk user.');
    }
}