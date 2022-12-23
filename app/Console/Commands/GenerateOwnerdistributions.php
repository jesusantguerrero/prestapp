<?php

namespace App\Console\Commands;

use App\Domains\Properties\Actions\GenerateInvoices;
use Illuminate\Console\Command;

class GenerateOwnerDistributions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:generate-owner-distributions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate owner distributions';

    /**
     * Execute the yeconsole command.
     *
     * @return int
     */
    public function handle()
    {
      return GenerateInvoices::forOwnerDistributions();
    }
}
