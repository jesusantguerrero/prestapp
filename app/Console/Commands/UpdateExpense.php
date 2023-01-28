<?php

namespace App\Console\Commands;

use App\Domains\Properties\Services\PropertyTransactionService;
use Illuminate\Console\Command;
use Insane\Journal\Models\Core\PaymentDocument;
use Insane\Journal\Models\Invoice\Invoice;

class UpdateExpense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-expense {invoiceId}';

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

        PropertyTransactionService::createOrUpdateExpense(
          $invoice->invoiceable,
          [
          'client_id' => $invoice->client_id,
          'account_id' => $invoice->account_id,
          'amount' => $invoice->total,
          'date' => $invoice->due_date,
          'details' => $invoice->concept,
          'concept' => $invoice->concept
        ],
          $invoice->id
        );

        return Command::SUCCESS;
    }
}
