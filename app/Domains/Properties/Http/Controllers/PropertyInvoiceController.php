<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\Properties\Exports\PaymentToTicket;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Invoice\Invoice;

class PropertyInvoiceController {
  public function printPayment(Invoice $invoice, Payment $payment) {
    $receipt = PaymentToTicket::getReceipt($invoice, $payment);
    $template = request()->query('type') ?? 'BareTicket';

    return inertia("Prints/$template", [
      "receipt" => $receipt,
      "user" => $payment->user,
    ]);
  }

}