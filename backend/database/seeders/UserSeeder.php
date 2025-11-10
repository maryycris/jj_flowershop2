<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create clerk user
        User::firstOrCreate([
            'email' => 'clerk@example.com',
        ], [
            'name' => 'Clerk User',
            'password' => Hash::make('password'),
            'role' => 'clerk',
        ]);

        // Create driver user
        User::firstOrCreate([
            'email' => 'driver@example.com',
        ], [
            'name' => 'Driver User',
            'password' => Hash::make('password'),
            'role' => 'driver',
        ]);

        // Create customer user
        User::firstOrCreate([
            'email' => 'customer@example.com',
        ], [
            'name' => 'Customer User',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
    }
}
