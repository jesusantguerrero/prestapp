<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\Properties\Services\RentTransactionService;

class GetSoBackInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-so-back-invoices {teamId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get invoices that should be paid back to SO';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $teamId = $this->argument('teamId');

        RentTransactionService::getSoBackInvoices($teamId);
        return Command::SUCCESS;
    }
}
