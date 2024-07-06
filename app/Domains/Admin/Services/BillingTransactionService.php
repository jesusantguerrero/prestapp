<?php

namespace App\Domains\Admin\Services;

use App\Models\Team;
use Insane\Treasurer\Models\Subscription;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Accounting\Helpers\InvoiceHelper;

class BillingTransactionService {
  public static function createInvoice($formData, Subscription $subscription, $withExtraServices = true) {
      $adminTeam = Team::admin();
      $additionalFees =  $subscription->services ?? [];
      $items = [[
        "name" => "Factura de subscriptiona",
        "concept" => "Factura de {$subscription->biller->name}",
        "quantity" => 1,
        "price" => $subscription->quantity,
        "amount" => $subscription->quantity,
      ]];

      $data = array_merge($formData, [
        'concept' =>  $formData['concept'] ?? 'Factura de subscriptiona',
        'description' => $formData['description'] ?? "Mensualidad {$subscription->biller->name}",
        'user_id' => $subscription->user_id,
        'team_id' => $adminTeam->id,
        'client_id' => $subscription->user_id,
        'invoiceable_id' => $subscription->id,
        'invoiceable_type' => Subscription::class,
        'date' => $formData['date'] ?? date('Y-m-d'),
        'type' => $formData['type'] ?? Invoice::DOCUMENT_TYPE_INVOICE,
        'category_type' => $formData['category_type'] ?? 'subscription',
        "invoice_account_id" => null,
        "account_id" => null,
        'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
        'total' =>  $formData['total'] ?? $formData['amount'] ?? $subscription->amount,
        'items' => array_merge($formData['items'] ?? $items,  $withExtraServices ? $additionalFees : []),
        "related_invoices" => $formData["related_invoices"] ?? []
      ]);

      if (isset($formData['payment_details'])) {
        $data['payment_details'] = $formData['payment_details'];
      }

      if (isset($formData['is_paid']) && $formData['is_paid']) {
        $data['payment_details'] = [
          'account_id' => $subscription->property->account_id,
          'concept' => "Pago {$data['concept']}",
          'payment_method' => $data['payment_method'] ?? 'cash'
        ];
      }

      return Invoice::createDocument($data);
    }
    public static function generateUpToDate($subscription, $areInvoicesPaid = false, $paidUntil = null) {
      $dateTarget = now()->format('Y-m-d');
      if ($subscription->end_date) {
        $dateTarget = $dateTarget >= $subscription->end_date ? $subscription->end_date : $dateTarget;
      }
      $nextDate = $subscription->next_invoice_date ?? $subscription->subscriptionInvoices()->latest('due_date')->first()->due_date;
      $generatedInvoices = [];

      echo "subscription of $subscription->client_name will be updated until $dateTarget: $subscription->end_date" . PHP_EOL;

      while ($nextDate && InvoiceHelper::getYearMonth($nextDate) <= InvoiceHelper::getYearMonth($dateTarget)) {
        $markAsPaid = $areInvoicesPaid && (!$paidUntil || $paidUntil >= $nextDate);
        $invoiceData = [
          'date' => $nextDate,
          'is_paid' => (boolean) $markAsPaid
        ];

        self::createInvoice($invoiceData, $subscription);
        $generatedInvoices[] = $nextDate;
        $nextDate = InvoiceHelper::getNextDate($nextDate)->format('Y-m-d');
      }

      $subscription->update([
        'next_invoice_date' => $nextDate,
        'generated_invoice_dates' => array_merge($subscription->generated_invoice_dates, $generatedInvoices)
      ]);
    }
}
