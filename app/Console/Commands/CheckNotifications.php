<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckNotifications extends Command
{
    protected $signature = 'notifications:check';
    protected $description = 'Check notification data structure';

    public function handle()
    {
        $customer = User::where('role', 'customer')->first();
        
        if (!$customer) {
            $this->error('No customer found');
            return;
        }

        $notifications = $customer->notifications()->latest()->take(3)->get();
        
        $this->info('Found ' . $notifications->count() . ' notifications for customer: ' . $customer->name);
        
        foreach ($notifications as $notification) {
            $this->line('---');
            $this->info('Notification ID: ' . $notification->id);
            $this->info('Type: ' . $notification->type);
            $this->info('Data: ' . json_encode($notification->data, JSON_PRETTY_PRINT));
        }
    }
}