<?php

namespace App\Domains\Properties\Services;

use App\Domains\Accounting\Helpers\InvoiceHelper;

class RentTransactionService {
    public  static function generatePendingInvoice($teamId, $isPaid = false) {
      $rents = (new RentService())->listWithInvoicesToGenerate($teamId)->get();

      echo "Rents to be updated ".count($rents);

      foreach ($rents as $rent) {
        self::generateUpToDate($rent, $isPaid);

        activity()
        ->performedOn($rent)
        ->log("Admin generated invoices for this rent from $rent->next_invoice_date");
      }
    }

    public static function generateUpToDate($rent, $areInvoicesPaid = false) {
      $dateTarget =  now()->format('Y-m-d');
      $nextDate = $rent->next_invoice_date;
      $generatedInvoices = [];

      echo "rent of $rent->client_name will be updated until $dateTarget" . PHP_EOL;

      while ($nextDate && $nextDate < $dateTarget) {
        $invoiceData = [
          'date' => $nextDate,
          'is_paid' => $areInvoicesPaid
        ];

        PropertyTransactionService::createInvoice($invoiceData, $rent);
        $generatedInvoices[] = $nextDate;
        $nextDate = InvoiceHelper::getNextDate($nextDate)->format('Y-m-d');
      }

      $rent->update([
        'next_invoice_date' => $nextDate,
        'generated_invoice_dates' => array_merge($rent->generated_invoice_dates, $generatedInvoices)
      ]);
    }

}
