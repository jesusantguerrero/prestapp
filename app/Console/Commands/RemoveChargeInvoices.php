<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\Properties\Services\RentTransactionService;

class RemoveChargeInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-charge-invoices {teamId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes charge invoices of cancelled transactions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $teamId = $this->argument('teamId');

        RentTransactionService::removeChargeInvoices($teamId);
        return Command::SUCCESS;
    }
}
