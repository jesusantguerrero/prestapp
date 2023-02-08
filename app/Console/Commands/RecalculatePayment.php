<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Core\PaymentDocument;

class RecalculatePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate-payment {paymentId} {--D|document}';

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
        $paymentId = $this->argument('paymentId');
        $isDocument = $this->option('document');
        if (!$isDocument) {
          $payment = Payment::find($paymentId);
          $payment->createTransaction();
        } else {
          $payment = PaymentDocument::find($paymentId);
          $payment->updateDocument();
        }

        return Command::SUCCESS;
    }
}
