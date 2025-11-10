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
    }
}


