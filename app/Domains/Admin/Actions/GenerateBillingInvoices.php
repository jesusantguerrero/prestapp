<?php

namespace App\Domains\Admin\Actions;

use App\Domains\Properties\Models\Rent;
use Insane\Treasurer\Models\Subscription;
use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Admin\Services\SubscriptionBillingService;
use App\Domains\Properties\Services\PropertyTransactionService;

class GenerateBillingInvoices {

    public static function scheduledRents() {
      $today = now()->format('Y-m-d');
      $plansWithInvoicesToCreate = Subscription::where(fn ($q) =>
        $q->whereRaw('DATE_FORMAT(next_billing_date, "%Y-%m") = DATE_FORMAT(?, "%Y-%m")', [$today])
        ->orWhereRaw("DATE_ADD(?, INTERVAL COALESCE(5, 0) DAY) >= next_billing_date", [$today])
      )
      ->whereNotIn('status', [Rent::STATUS_CANCELLED, Rent::STATUS_PAID, Rent::STATUS_EXPIRED])
      ->get();

      foreach ($plansWithInvoicesToCreate as $rent) {
        SubscriptionBillingService::generateNextInvoice($rent);
      }
    }

    public static function forceNextRents() {
      $rentWithInvoicesToCreate = Rent::whereNotNull('next_invoice_date')
      ->whereNotIn('status', [Rent::STATUS_CANCELLED, Rent::STATUS_PAID])
      ->get();

      foreach ($rentWithInvoicesToCreate as $rent) {
        if (!in_array($rent->next_invoice_date, $rent->generated_invoice_dates))  {
          PropertyTransactionService::createInvoice([
            'date' => $rent->next_invoice_date
          ], $rent);
          $rent->update([
            'next_invoice_date' => InvoiceHelper::getNextDate($rent->next_invoice_date),
            'generated_invoice_dates' => array_merge()
          ]);
        }
      }
    }
}
