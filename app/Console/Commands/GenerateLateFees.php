<?php

namespace App\Console\Commands;

use App\Domains\Properties\Actions\GenerateInvoices;
use App\Domains\Properties\Actions\UpdateLateInvoices;
use Illuminate\Console\Command;

class GenerateLateFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:generate-late-fees';

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
      return GenerateInvoices::chargeLateFees();
    }
}
