<?php

namespace App\Console\Commands;

use App\Domains\CRM\Models\Client;
use Illuminate\Console\Command;

class SeedDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-demo {resource} {count=10}';

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

      $resource = $this->argument('resource');
      $count = $this->argument('count');
      if ($resource == 'tenant') {
        Client::factory([
          "team_id" => 1,
          "user_id" => 1,
          "is_tenant" => 1
        ])
        ->count($count)
        ->create();
      }
        return Command::SUCCESS;
    }
}
