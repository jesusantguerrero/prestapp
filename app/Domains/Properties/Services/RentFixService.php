<?php

namespace App\Domains\Properties\Services;

use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Invoice\Invoice;

class RentFixService {

    public static function invoicesZeroLateFees($teamId) {
      return Invoice::where('invoiceable_type', Rent::class)
      ->where('team_id', $teamId)
      ->with(['invoiceable'])
      ->where('total', 0)
      ->whereRaw("concept like '%mora%'");
    }

    public  static function removeZeroLateFeeInvoices($teamId) {

      foreach (self::invoicesZeroLateFees($teamId)->get() as $invoice) {
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
        ->withProperties([
          "rent_id" => $rentId,
          "client_id" => $clientName,
          "orphans" => $amount,
          "debt" => $debt,
          "date" => $date,
          "dueDate" => $dueDate
        ])
        ->log("$author deleted late invoice  rent $rentId of $clientName");
      }
    }

    public static function removeInvoicesAfterDate($teamId, $date) {
      $invoices = Invoice::where('invoiceable_type', Rent::class)
      ->where('team_id', $teamId)
      ->with(['invoiceable'])
      ->where("status", Invoice::STATUS_UNPAID)
      ->where('date', '>', $date)
      ->get();

      foreach ($invoices as $invoice) {
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
        ->withProperties([
          "rent_id" => $rentId,
          "client_id" => $clientName,
          "orphans" => $amount,
          "debt" => $debt,
          "date" => $date,
          "dueDate" => $dueDate
        ])
        ->log("$author deleted late invoice  rent $rentId {$invoice->date} of $clientName");
    }
  }

}
