<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update-role {user_id} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a user\'s role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $role = $this->argument('role');

        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $user->role = $role;
        $user->save();

        $this->info("Successfully updated user {$user->name}'s role to {$role}");
        return 0;
    }
}
