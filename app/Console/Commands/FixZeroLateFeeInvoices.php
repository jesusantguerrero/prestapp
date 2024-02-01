<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\Properties\Services\RentFixService;

class FixZeroLateFeeInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-zero-late-fee-invoices {teamId}';

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

        RentFixService::removeZeroLateFeeInvoices($teamId);
        return Command::SUCCESS;
    }
}
