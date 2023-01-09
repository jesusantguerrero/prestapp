<?php

namespace App\Domains\Properties\Exports;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Invoice\Invoice;

class InvoiceToTicket {
  private $processor = null;

  public function process(Invoice $invoice) {
    $this->processor =[
        "invoice" => array_merge($invoice->toArray(),[
        'lines' => $invoice->lines,
        'client' => $invoice->client,
        'discounts' => $invoice->taxesLines()->groupBy('tax_id')->select(
          DB::raw('sum(amount * type) as amount, concept')
        )->get()]),
        "company" => Setting::getBySection($invoice->team_id, 'business'),
    ];
  }


  public function previewAs($filename = 'file') {
    return inertia('Prints/Ticket', $this->processor);
  }
}
