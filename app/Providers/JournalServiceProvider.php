<?php

namespace App\Providers;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Exports\InvoiceToPdf;
use Illuminate\Support\ServiceProvider;
use Insane\Journal\Journal;

class JournalServiceProvider extends ServiceProvider
{

     /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Journal::useCustomerModel(Client::class);
        Journal::printInvoiceUsing(InvoiceToPdf::class);
    }
}
