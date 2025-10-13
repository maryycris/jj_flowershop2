<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\EnhancedNotificationSeeder;

class SeedEnhancedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:seed-enhanced';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed enhanced notifications with proper messages and clickable functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding enhanced notifications...');
        
        $seeder = new EnhancedNotificationSeeder();
        $seeder->run();
        
        $this->info('Enhanced notifications seeded successfully!');
        
        return 0;
    }
}