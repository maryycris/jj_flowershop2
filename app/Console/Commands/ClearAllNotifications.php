<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ClearAllNotifications extends Command
{
    protected $signature = 'notifications:clear-all';
    protected $description = 'Clear all notifications for all users';

    public function handle()
    {
        // Clear all notifications
        User::all()->each(function($user) {
            $user->notifications()->delete();
        });

        $this->info('All notifications cleared for all users');
        
        // Show user counts
        $adminCount = User::where('role', 'admin')->count();
        $clerkCount = User::where('role', 'clerk')->count();
        $customerCount = User::where('role', 'customer')->count();
        
        $this->info("Users found: {$adminCount} admins, {$clerkCount} clerks, {$customerCount} customers");
    }
}
