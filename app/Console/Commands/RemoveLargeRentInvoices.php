<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\Properties\Services\RentTransactionService;

class RemoveLargeRentInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-large-invoices {teamId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes invoices of large rents';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $teamId = $this->argument('teamId');

        RentTransactionService::removeOverContractedRentInvoice($teamId);
        return Command::SUCCESS;
    }
}
