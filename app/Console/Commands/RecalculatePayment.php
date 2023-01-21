<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Insane\Journal\Models\Core\Payment;

class RecalculatePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate-payment {paymentId}';

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
        $payment = Payment::find($paymentId);
        $payment->createTransaction();

        return Command::SUCCESS;
    }
}
