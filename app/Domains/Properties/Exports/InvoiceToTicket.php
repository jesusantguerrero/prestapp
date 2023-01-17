<?php

namespace App\Domains\Properties\Exports;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Insane\Journal\Models\Invoice\Invoice;

class InvoiceToTicket {
  private $processor = null;
  public function getLines($invoice) {
    return $invoice->lines->filter(function($line) {
      return !Str::contains($line->concept, ['Déposito', 'Devolución']) && $line->amount > 0;
    });
  }

  public function getLineGroups($invoice) {
    return $invoice->lines->filter(function($line) {
      return Str::contains($line->concept, 'Déposito');
    });
  }

  public function getDiscounts($invoice) {
    $taxes = $invoice->taxesLines()->groupBy('tax_id')->select(
      DB::raw('sum(amount * type) as amount, concept')
    )->get()->toArray();
    return array_merge($taxes, $invoice->lines->filter(function($line) {
      return $line->amount < 0;
    })->toArray());
  }

  public function process(Invoice $invoice) {
    $lines = $this->getLines($invoice);
    $lineGroups = $this->getLineGroups($invoice);

    $this->processor =[
        "invoice" => array_merge($invoice->toArray(),[
          'lines' => $lines,
          'client' => $invoice->client,
          "lineGroups" => [
            'Descuentos' => $this->getDiscounts($invoice),
            "Depositos" => $lineGroups
          ],
        ]),
        "company" => Setting::getBySection($invoice->team_id, 'business'),
    ];
  }


  public function previewAs($filename = 'file') {
    return inertia('Prints/Ticket', $this->processor);
  }
}
