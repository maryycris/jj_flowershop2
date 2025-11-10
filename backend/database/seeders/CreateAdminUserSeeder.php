<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create or update an admin user with a known password
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'sex' => 'M',
                'contact_number' => 'N/A',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'is_verified' => true,
                'verification_code' => null,
                'verification_expires_at' => null,
            ]
        );

        // Create or update a clerk user
        User::updateOrCreate(
            ['email' => 'clerk@example.com'],
            [
                'name' => 'Clerk User',
                'username' => 'clerk',
                'role' => 'clerk',
                'password' => Hash::make('password'),
                'sex' => 'M',
                'contact_number' => 'N/A',
                'first_name' => 'Clerk',
                'last_name' => 'User',
                'is_verified' => true,
                'verification_code' => null,
                'verification_expires_at' => null,
            ]
        );

        // Create or update a driver user
        User::updateOrCreate(
            ['email' => 'driver@example.com'],
            [
                'name' => 'Driver User',
                'username' => 'driver',
                'role' => 'driver',
                'password' => Hash::make('password'),
                'sex' => 'M',
                'contact_number' => 'N/A',
                'first_name' => 'Driver',
                'last_name' => 'User',
                'is_verified' => true,
                'verification_code' => null,
                'verification_expires_at' => null,
            ]
        );

        // Create or update a test customer user (for testing)
        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer User',
                'email' => 'customer@example.com',
                'role' => 'customer',
                'password' => Hash::make('password'),
                'sex' => 'M',
                'contact_number' => '09123456789',
                'first_name' => 'Customer',
                'last_name' => 'User',
                'is_verified' => true,
                'verification_code' => null,
                'verification_expires_at' => null,
            ]
        );
    }
}


