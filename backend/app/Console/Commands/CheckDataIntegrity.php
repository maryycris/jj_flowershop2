<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Driver;

class CheckDataIntegrity extends Command
{
    protected $signature = 'data:check-integrity';
    protected $description = 'Check for data integrity issues between users and drivers';

    public function handle()
    {
        $this->info('Checking data integrity...');
        
        // Check for users with driver role but no driver record
        $usersWithDriverRoleButNoDriverRecord = User::where('role', 'driver')
            ->whereDoesntHave('driver')
            ->get();
            
        if ($usersWithDriverRoleButNoDriverRecord->count() > 0) {
            $this->warn('Found users with driver role but no driver record:');
            foreach ($usersWithDriverRoleButNoDriverRecord as $user) {
                $this->line("- {$user->name} (ID: {$user->id})");
            }
        }
        
        // Check for users with driver record but wrong role
        $driversWithWrongRole = Driver::with('user')
            ->whereHas('user', function($query) {
                $query->where('role', '!=', 'driver');
            })
            ->get();
            
        if ($driversWithWrongRole->count() > 0) {
            $this->warn('Found driver records with users having wrong role:');
            foreach ($driversWithWrongRole as $driver) {
                $this->line("- Driver ID: {$driver->id}, User: {$driver->user->name} (Role: {$driver->user->role})");
            }
        }
        
        if ($usersWithDriverRoleButNoDriverRecord->count() == 0 && $driversWithWrongRole->count() == 0) {
            $this->info('✅ Data integrity check passed! No issues found.');
        } else {
            $this->error('❌ Data integrity issues found. Please fix them.');
        }
        
        return 0;
    }
}