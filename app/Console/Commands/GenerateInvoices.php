<?php

namespace App\Console\Commands;

use App\Domains\Properties\Actions\GenerateInvoices as ActionsGenerateInvoices;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:generate-invoices {--N|next-invoices} {--L|late-fees}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for rent services';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $nextInvoices = $this->option('next-invoices');
      $lateFees = $this->option('next-invoices');

      if ($nextInvoices) {
        return GenerateInvoices::forceNextRents();
      }

      if ($lateFees) {
        return GenerateInvoices::chargeLateFees();
      }

      return ActionsGenerateInvoices::scheduledRents();
    }
}
