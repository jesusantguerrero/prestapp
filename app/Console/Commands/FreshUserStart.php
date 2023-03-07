<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FreshUserStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:demo-fresh-start';

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
      Artisan::call('migrate:fresh --seed');
      User::factory(['email' => 'demo@icloan.com'])->withPersonalTeam()->create();
      Artisan::call('app:seed-demo owner 3');
      Artisan::call('app:seed-demo tenant 3');
      return Command::SUCCESS;
    }
}
