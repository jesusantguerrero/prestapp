<?php

namespace App\Console\Commands;

use App\Domains\Properties\Services\RentTransactionService;
use Illuminate\Console\Command;

class PayOverdueRentInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pay-overdue-rent-invoices {teamId} {date}';

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

      RentTransactionService::payOverdueInvoicesAsOf($teamId, $date);
      return Command::SUCCESS;
    }
}
