<?php

namespace App\Console\Commands;

use App\Domains\Properties\Services\RentTransactionService;
use Illuminate\Console\Command;

class GeneratePreviousRents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-previous-invoices {teamId} {date?} {--P|paid}';

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

      $teamId = $this->argument('teamId');
      $teamId = $this->argument('teamId');
      $isPaid = $this->option('paid');

      RentTransactionService::generatePendingInvoice($teamId, $isPaid);
      return Command::SUCCESS;
    }
}
