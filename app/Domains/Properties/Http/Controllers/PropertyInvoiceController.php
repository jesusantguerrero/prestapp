<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\Properties\Exports\PaymentToTicket;
use App\Domains\Properties\Models\Rent;
use Exception;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Invoice\Invoice;
use Insane\Journal\Services\InvoiceService;

class PropertyInvoiceController {
  public function printPayment(Invoice $invoice, Payment $payment) {
    $receipt = PaymentToTicket::getReceipt($invoice, $payment);
    $template = request()->query('type') ?? 'BareTicket';

    return inertia("Prints/$template", [
      "receipt" => $receipt,
      "user" => $payment->user,
    ]);
  }

  public function simpleUpdate(Rent $rent, Invoice $invoice, InvoiceService $invoiceService) {
    try {
      if ($invoice->team_id != request()->user()->current_team_id) return;
      $invoice = $invoiceService->update($invoice, request()->post());
      return $invoice;
    } catch (Exception $e) {
      return response([
        "error" => [
          "message" => $e->getMessage()
        ]
      ], 404);
    }
  }
}