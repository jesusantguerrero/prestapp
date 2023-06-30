<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:health-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the health of all the services of the application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Health check started');
        // your logic
        Log::info('Health check ended');
        return Command::SUCCESS;
    }
}
