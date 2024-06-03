<?php

namespace App\Console\Commands\Manager;

use App\Models\User;
use Illuminate\Console\Command;
use App\Domains\Admin\Services\AdminTeamService;

class Users extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manager:users {userEmail} {--D|disable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable or Enable users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $isDisabled = $this->option('disable');
      $userEmail =  $this->argument('userEmail');
      $user = User::where('email', $userEmail);

      $teamService = new AdminTeamService();

      if ($isDisabled) {
          $teamService->disable($user->currentTeam->id);
        return;
      }

      $teamService->approve($user->currentTeam->id);
    }
}
