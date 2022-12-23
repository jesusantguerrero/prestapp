<?php

namespace App\Console\Commands;

use App\Domains\Properties\Actions\GenerateInvoices;
use Illuminate\Console\Command;

class GenerateNextInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:generate-next-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate next invoices for rent services';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      return GenerateInvoices::forceNextRents();
    }
}
