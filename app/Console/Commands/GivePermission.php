<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class GivePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:give-permission {userId} {permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give a specific permission to a user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $permissionName = $this->argument('permission');
        
        $user = User::findOrFail($userId);
        
        // Create permission if it doesn't exist
        Permission::firstOrCreate(['name' => $permissionName]);
        
        // Give the permission to the user
        $user->givePermissionTo($permissionName);
        
        $this->info("Permission '{$permissionName}' has been granted to user {$user->name}");

        return Command::SUCCESS;
    }
}
