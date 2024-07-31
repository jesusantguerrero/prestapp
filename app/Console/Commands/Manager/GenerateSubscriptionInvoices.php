<?php

namespace App\Console\Commands\Manager;

use Illuminate\Console\Command;
use App\Domains\Admin\Actions\GenerateBillingInvoices;

class GenerateSubscriptionInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:generate-subscription-invoices {--N|next-invoices}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for subscription services';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $nextInvoices = $this->option('next-invoices');

      if ($nextInvoices) {
        return GenerateBillingInvoices::forceNextRents();
      }

      return GenerateBillingInvoices::scheduledRents();
    }
}
