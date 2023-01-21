<?php

namespace App\Console\Commands;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use App\Domains\Properties\Models\Rent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Category;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Core\Transaction;
use Insane\Journal\Models\Core\TransactionLine;
use Insane\Journal\Models\Invoice\Invoice;
use Insane\Journal\Models\Invoice\InvoiceLine;

class DeleteTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-transactions {teamId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all the transactions of a team';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Transaction::truncate();
        TransactionLine::truncate();
        Payment::truncate();
        Invoice::truncate();
        InvoiceLine::truncate();

        // app
        LoanInstallment::truncate();
        Loan::truncate();
        Rent::truncate();
        Account::truncate();
        Category::truncate();

        $teamId = $this->argument('teamId');

        Artisan::call('journal:set-accounts');
        Artisan::call("journal:set-chart-accounts $teamId");

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return Command::SUCCESS;
    }
}
