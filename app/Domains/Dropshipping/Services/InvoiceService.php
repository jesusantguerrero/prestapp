<?php

namespace App\Domains\Dropshipping\Services;

use Illuminate\Support\Str;
use App\Domains\CRM\Models\Client;
use App\Domains\Dropshipping\Models\Order;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Dropshipping\Data\OrderData;

class InvoiceService
{

    public function sanitizeData($formData, Transaction $invoice = null) {
      $clientId = $formData["client_id"] ?? null;
      $isNewPayee = Str::contains($clientId, "new::");

      if ($clientId == 'new' || $isNewPayee) {
          $label = trim(str_replace('new::', '',  $clientId)) ?? 'General Provider';
          $client = Client::findOrCreateByName($invoice?->toArray() ?? $formData, $label);
          $formData["client_id"] = $client->id;
      }

      return $formData;
  }

  public function create(array $formData, $user) {
      $formData = $this->sanitizeData($formData);

      $data = array_merge($formData, [
        'concept' =>  $formData['concept'] ?? 'Factura general',
        'description' => $formData['description'] ?? "Venta general",
        'user_id' => $user->id,
        'team_id' => $user->current_team_id,
        'client_id' => $formData["client_id"],
        'invoiceable_id' => null,
        'invoiceable_type' => null,
        'date' => $formData['date'] ?? date('Y-m-d'),
        'type' => $formData['type'] ?? Invoice::DOCUMENT_TYPE_INVOICE,
        'category_type' => 'sales',
        "invoice_account_id" => null,
        "account_id" => null,
        'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
        'total' =>  $formData['total'] ?? 0,
        'items' => $formData['lines'] ?? [],
        "related_invoices" => $formData["related_invoices"] ?? []
      ]);

      if (isset($formData['payment_details'])) {
        $data['payment_details'] = $formData['payment_details'];
      }

      if (isset($formData['is_paid']) && $formData['is_paid']) {
        $data['payment_details'] = [
          'account_id' => $data['payment_account_id'] ?? null,
          'concept' => "Pago {$data['concept']}",
          'payment_method' => $data['payment_method'] ?? 'cash'
        ];
      }

      return Invoice::createDocument($data);
    }

    public function getOrderById(int $invoiceId) {
      $invoice = Invoice::find($invoiceId);
      return $invoice->getInvoiceData();
    }

    public function send(Order $order) {
      $order->state()->send();
    }

    public function markAsReceived(Order $order) {
      $order->state()->markAsReceived();
    }

    public function cancel(Order $order) {
      $order->state()->cancel();
    }

    public function return(Order $order) {
      $order->state()->return();
    }
}
