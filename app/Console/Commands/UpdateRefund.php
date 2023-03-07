<?php

namespace App\Console\Commands;

use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyTransactionService;
use Illuminate\Console\Command;
use Insane\Journal\Models\Invoice\Invoice;

class UpdateRefund extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-refund {invoiceId}';

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
        $invoiceId = $this->argument('invoiceId');
        $invoice = Invoice::find($invoiceId);

        if ($invoice->invoiceable_type === Rent::class) {
          $rent = Rent::find($invoice->invoiceable_id);
        }


        PropertyTransactionService::createDepositRefund($rent, [
          'payments' => [[
            'amount' => $invoice->total
          ]]
        ],
        $invoice);

        return Command::SUCCESS;
    }
}
