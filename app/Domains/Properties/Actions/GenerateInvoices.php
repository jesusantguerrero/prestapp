<?php

namespace App\Domains\Properties\Actions;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Properties\Services\RentService;
use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Services\PropertyTransactionService;

class GenerateInvoices {

    public static function scheduledRents() {
      $rentWithInvoicesToCreate = Rent::where(function ($q) {
        $q->whereRaw('DATE_FORMAT(next_invoice_date, "%Y-%m") = DATE_FORMAT(curdate(),  "%Y-%m")')
        ->orWhereRaw("DATE_ADD(curdate(), INTERVAL COALESCE(5, 0) DAY) < next_invoice_date");
      })
      ->whereNotIn('status', [Rent::STATUS_CANCELLED, Rent::STATUS_PAID, Rent::STATUS_EXPIRED])
      ->get();

      foreach ($rentWithInvoicesToCreate as $rent) {
        RentService::generateNextInvoice($rent);
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
      $lateInvoices = Invoice::selectRaw(
        'invoices.*,
        rents.id as rentId,
        rents.grace_days as rentGraceDays,
        DATE_ADD(due_date, INTERVAL COALESCE(rents.grace_days - 2, 0) DAY)  as lateFeeAt'
      )
      ->whereRaw('debt > 0 AND DATE_ADD(due_date, INTERVAL COALESCE(rents.grace_days - 2, 0) DAY) < curdate() AND rents.late_fee > 0')
      ->join('rents', 'invoiceable_id', 'rents.id')
      ->where('invoiceable_type', Rent::class)
      ->where('category_type', PropertyInvoiceTypes::Rent->value)
      // ->when(!$forceCharge, fn ($query) => $query->whereNot('invoices.status', Invoice::STATUS_OVERDUE))
      ->get();

      // dd($lateInvoices->map(fn ($li) => $li->description . " " . $li->lateFeeAt));

      if (count($lateInvoices)) {
          PropertyTransactionService::createLateFees([$lateInvoices->last()]);
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
