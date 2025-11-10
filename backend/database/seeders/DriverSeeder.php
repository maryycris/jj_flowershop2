<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Driver;

class DriverSeeder extends Seeder
{
    public function run()
    {
        // Only create driver profiles for existing users with role 'driver'
        // Admin should create the users first, then run this seeder to create driver profiles
        
        $driverUsers = User::where('role', 'driver')->get();
        
        if ($driverUsers->isEmpty()) {
            $this->command->info('No driver users found. Please create driver users in admin panel first.');
            return;
        }

        foreach ($driverUsers as $index => $user) {
            // Check if driver profile already exists
            if (!Driver::where('user_id', $user->id)->exists()) {
                Driver::create([
                    'user_id' => $user->id,
                    'license_number' => 'DL' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'vehicle_type' => ['Motorcycle', 'Car', 'Van'][$index % 3],
                    'vehicle_plate' => 'ABC-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'availability_status' => 'available',
                    'work_start_time' => '08:00',
                    'work_end_time' => '17:00',
                    'max_deliveries_per_day' => 15,
                    'current_deliveries_today' => 0
                ]);
                
                $this->command->info("Created driver profile for: {$user->name}");
            } else {
                $this->command->info("Driver profile already exists for: {$user->name}");
            }
        }
    }
}