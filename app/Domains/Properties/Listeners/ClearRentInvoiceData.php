<?php

namespace App\Domains\Properties\Listeners;

use App\Domains\Properties\Services\RentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ClearRentInvoiceData
{
    public function handle(InvoiceDeleted $event): void
    {
        if ($event->invoice->invoiceable_type == Rent::class) {
          RentService::clearInvoiceData($event->invoiceable);
        }
    }
}
