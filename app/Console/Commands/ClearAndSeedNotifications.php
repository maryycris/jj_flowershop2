<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Database\Seeders\SampleNotificationSeeder;

class ClearAndSeedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all notifications and seed enhanced notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing all existing notifications...');
        DB::table('notifications')->delete();
        
        $this->info('Seeding sample notifications...');
        $seeder = new SampleNotificationSeeder();
        $seeder->run();
        
        $this->info('✅ Notifications refreshed successfully!');
        $this->info('Now go to /customer/notifications to see the enhanced notifications.');
        
        return 0;
    }
}