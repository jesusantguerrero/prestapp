<?php

namespace App\Console\Commands;

use App\Domains\Properties\Services\RentService;
use Illuminate\Console\Command;

class CheckExpiredRents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:check-expired-rents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check expiring rents and send notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      RentService::updateExpiredRents();
      return Command::SUCCESS;
    }
}
