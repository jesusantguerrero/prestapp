<?php

namespace App\Console\Commands;

use App\Domains\Loans\Actions\UpdateLatePayments;
use Illuminate\Console\Command;

class GenerateLoanFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:generate-loan-fees';

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
      return UpdateLatePayments::run();
    }
}
