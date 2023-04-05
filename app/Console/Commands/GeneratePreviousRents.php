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
      $date = $this->argument('date');
      $isPaid = $this->option('paid');

      // TODO: write test
      RentTransactionService::generatePendingInvoice($teamId, $isPaid, $date);
      return Command::SUCCESS;
    }
}
