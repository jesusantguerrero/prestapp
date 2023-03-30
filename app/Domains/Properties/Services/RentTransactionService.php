<?php

namespace App\Domains\Properties\Services;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Invoice\Invoice;

class RentTransactionService {
    public  static function orphansInvoices($teamId, $with = []) {
      return Invoice::where('invoiceable_type', Rent::class)
      ->where('team_id', $teamId)
      ->with(['invoiceable'])
      ->whereDoesntHave('invoiceable');
    }

    public  static function removeOrphansInvoices($teamId) {
      $invoices = self::orphansInvoices($teamId, ['client'])->get();
      echo count($invoices);

      foreach (self::orphansInvoices($teamId, ['client'])->get() as $invoice) {
        $invoice->delete();

        $rentId = (string) $invoice->invoiceable_id;
        $clientName = $invoice->client->display_name;
        $amount = (string) $invoice->total;
        $debt = (string) $invoice->debt;
        $date = $invoice->date;
        $dueDate = $invoice->due_date;

        $author = auth()?->user()?->name ?? "Admin";

        echo "$clientName invoice $rentId deleted" . PHP_EOL;

        activity()
        ->performedOn($invoice)
        ->withProperties(compact($rentId, $clientName, $amount, $debt, $date, $dueDate))
        ->log("$author deleted orphan invoice for this the rent from $rentId of $clientName");
      }
    }

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
