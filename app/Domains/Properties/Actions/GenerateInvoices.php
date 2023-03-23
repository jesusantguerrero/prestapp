<?php

namespace App\Domains\Properties\Actions;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyTransactionService;
use Insane\Journal\Models\Invoice\Invoice;

class GenerateInvoices {

    public static function scheduledRents() {
      $rentWithInvoicesToCreate = Rent::whereRaw('next_invoice_date - curdate() <= 31')
      ->whereNotIn('status', [Rent::STATUS_CANCELLED, Rent::STATUS_PAID])
      ->get();

      foreach ($rentWithInvoicesToCreate as $rent) {
        if (!in_array($rent->next_invoice_date, $rent->generated_invoice_dates))  {
          PropertyTransactionService::createInvoice([
            'date' => $rent->next_invoice_date
          ], $rent);
          $rent->update([
            'next_invoice_date' => InvoiceHelper::getNextDate($rent->next_invoice_date),
            'generated_invoice_dates' => array_merge($rent->next_invoice_date)
          ]);
        }
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

    public static function chargeLateFees(bool $forceCharge = false) {
      $lateInvoices = Invoice::select(['invoices.*','rents.id as rentId', 'rents.grace_days as rentGraceDays'])
      ->whereRaw('debt > 0 AND DATE_ADD(due_date, INTERVAL COALESCE(rents.grace_days, 0) DAY) < curdate()')
      ->join('rents', 'invoiceable_id', 'rents.id')
      ->where('invoiceable_type', Rent::class)
      ->when(!$forceCharge, fn ($query) => $query->whereNot('invoices.status', 'overdue'))
      ->get();

      if (count($lateInvoices)) {
          PropertyTransactionService::createLateFees($lateInvoices);
      }
    }

    public static function forOwnerDistributions() {
      $clientWithPendingDistributions = Client::whereNotNull('owner_distribution_date')
      ->whereRaw("DATE_FORMAT(curdate(), concat('%Y-%m-', clients.owner_distribution_date)) = curdate()",)
      ->get();
      if (count($clientWithPendingDistributions)) {
        foreach ($clientWithPendingDistributions as $client) {
          if (!in_array(date('Y-m-d'), $client->generated_distribution_dates))  {
            PropertyTransactionService::createOwnerDistribution($client);
          }
        }
      }
    }
}
