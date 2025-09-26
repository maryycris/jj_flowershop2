<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create driver users
        $drivers = [
            [
                'name' => 'John Driver',
                'email' => 'driver1@example.com',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'phone' => '09123456789',
            ],
            [
                'name' => 'Mike Delivery',
                'email' => 'driver2@example.com',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'phone' => '09123456790',
            ],
            [
                'name' => 'Sarah Transport',
                'email' => 'driver3@example.com',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'phone' => '09123456791',
            ],
        ];

        foreach ($drivers as $driverData) {
            User::updateOrCreate(
                ['email' => $driverData['email']],
                $driverData
            );
        }
    }
}