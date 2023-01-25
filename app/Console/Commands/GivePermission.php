<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;

class GivePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:give-permission {userId} ${permissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $permissions = $this->argument('permissions');
        $user = User::find($userId);

        setPermissionsTeamId(Team::find($user->current_team_id));
        $user->assignRole($permissions, []);
        // $user->givePermission(implode(',', $permissions));

        return Command::SUCCESS;
    }
}
