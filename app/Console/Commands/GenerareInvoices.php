<?php

namespace App\Console\Commands;

use App\Domains\Properties\Actions\UpdateLateInvoices;
use Illuminate\Console\Command;

class GenerareInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:generate-invoices';

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
      // if ($next) {
        return UpdateLateInvoices::generateNextInvoices();
      // } 
      // return UpdateLateInvoices::generateScheduledInvoices();
    }
}
