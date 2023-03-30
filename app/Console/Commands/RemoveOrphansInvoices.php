<?php

namespace App\Console\Commands;

use App\Domains\Properties\Services\RentTransactionService;
use Illuminate\Console\Command;

class RemoveOrphansInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-orphans-rent-invoices {teamId}';

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

        RentTransactionService::removeOrphansInvoices($teamId);
        return Command::SUCCESS;
    }
}
