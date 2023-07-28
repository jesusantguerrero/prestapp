<?php

namespace App\Domains\Properties\Exports;

use App\Domains\Accounting\DTO\ReceiptData;
use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Atmosphere\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Invoice\Invoice;

class PaymentToTicket {
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

  public static function getReceipt(Invoice $invoice, Payment $payment) {
    $documentData = $payment->toArray();
    $documentData['resource_name'] = 'Factura';
    $documentData['client_name'] = $invoice->client->display_name;
    $documentData['total_in_words'] = InvoiceHelper::numberToWords($payment->amount);

    $receipt = new ReceiptData(
      Setting::getBySection($invoice->team_id, 'business'),
      $invoice->client,
      $payment->concept,
      [[
          "concept" => $payment->concept,
          "amount" => $payment->amount
      ]],
      $payment->amount,
      $payment->meta_data,
      "**Verifique su recibo valor no reembolsable**"
    );

    return $receipt;
  }
}
