<?php

namespace App\Domains\Properties\Services;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Invoice\Invoice;

class RentTransactionService {
    public  static function orphansInvoices($teamId, $with = []) {
      return Invoice::where('invoiceable_type', Rent::class)
      ->where('team_id', $teamId)
      ->with(['invoiceable'])
      ->whereDoesntHave('invoiceable');
    }

    public static function invoicesAsOf($teamId, $date, $status = [Invoice::STATUS_OVERDUE, Invoice::STATUS_UNPAID]) {
      return Invoice::where('invoiceable_type', Rent::class)
      ->where('team_id', $teamId)
      ->with(['invoiceable'])
      ->where('due_date', '<=', $date)
      ->whereIn('status', $status);
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
        ->withProperties([
          "rent_id" => $rentId,
          "client_id" => $clientName,
          "orphans" => $amount,
          "debt" => $debt,
          "date" => $date,
          "dueDate" => $dueDate
        ])
        ->log("$author deleted orphan invoice for this the rent from $rentId of $clientName");
      }
    }

    public  static function generatePendingInvoice($teamId, $isPaid, $paidDate = false) {
      $rents = (new RentService())->listWithInvoicesToGenerate($teamId)->get();

      echo "Rents to be updated ".count($rents);

      foreach ($rents as $rent) {
        $isPaid = (boolean) $paidDate;
        self::generateUpToDate($rent, $isPaid, $paidDate);

        activity()
        ->performedOn($rent)
        ->log("Admin generated invoices for this rent from $rent->next_invoice_date");
      }
    }

    public  static function fixPaymentDates($teamId) {
      $payments = Payment::where('payments.team_id', $teamId)
      ->where('payable_type', Invoice::class)
      ->whereRaw('invoices.due_date <> payments.payment_date')
      ->join('invoices', 'invoices.id', '=', 'payments.payable_id')
      ->with(['payable'])
      ->get();

      echo "Payments to be updated ".count($payments);

      foreach ($payments as $payment) {
        $oldDate = $payment->payment_date;
        $payment->update([
          'payment_date' => $payment->payable->due_date
        ]);
        $payment->createTransaction();

        activity()
        ->performedOn($payment)
        ->log("Admin updated payment date from $oldDate to $payment->payment_date");
      }
    }

    public static function payOverdueInvoicesAsOf($teamId, $date) {
      $invoices = self::invoicesAsOf($teamId, $date)->get();

      echo "Invoices to be paid ".count($invoices);

      foreach ($invoices as $invoice) {
        if ($invoice->invoiceable) {
          RentService::payInvoice($invoice->invoiceable, $invoice, [
            'amount' => $invoice->debt ?? $invoice->total,
            'payment_date' => $invoice->due_date,
            'client_id' => $invoice->client_id,
            'amount' => $invoice->debt,
            'details' => 'Pago de ' . $invoice->concept,
            'concept' => 'Pago de ' . $invoice->concept,
          ]);

          activity()
          ->performedOn($invoice)
          ->log("Admin paid invoice from {$invoice->client->display_name} of {$invoice->due_date}");
        } else {
          activity()
          ->performedOn($invoice)
          ->log("Admin couldn't pay invoice from {$invoice->client->display_name} of {$invoice->due_date}");
        }
      }
    }

    public static function generateUpToDate($rent, $areInvoicesPaid = false, $paidUntil = null) {
      $dateTarget = now()->format('Y-m-d');
      if ($rent->end_date) {
        $dateTarget = $dateTarget >= $rent->end_date ? $rent->end_date : $dateTarget;
      }
      $nextDate = $rent->next_invoice_date;
      $generatedInvoices = [];

      echo "rent of $rent->client_name will be updated until $dateTarget: $rent->end_date" . PHP_EOL;

      while ($nextDate && InvoiceHelper::getYearMonth($nextDate) <= InvoiceHelper::getYearMonth($dateTarget)) {
        $markAsPaid = $areInvoicesPaid && (!$paidUntil || $paidUntil >= $nextDate);
        $invoiceData = [
          'date' => $nextDate,
          'is_paid' => (boolean) $markAsPaid
        ];

        echo "Date $nextDate $markAsPaid:  $paidUntil $nextDate" . PHP_EOL;

        PropertyTransactionService::createInvoice($invoiceData, $rent);
        $generatedInvoices[] = $nextDate;
        $nextDate = InvoiceHelper::getNextDate($nextDate)->format('Y-m-d');
      }

      $rent->update([
        'next_invoice_date' => $nextDate,
        'generated_invoice_dates' => array_merge($rent->generated_invoice_dates, $generatedInvoices)
      ]);
    }

    public static function removeExpirationInvoices($teamId = null) {
      $expiredRents = RentService::expiredRents($teamId, 'expired');

      foreach ($expiredRents as $expiredRent) {
        $postExpirationInvoices = $expiredRent->postExpirationInvoices();
        $count = count($postExpirationInvoices);
        echo "$expiredRent->client_name has {$count} invoices post expiration from {$postExpirationInvoices->last()?->due_date} to {$postExpirationInvoices->first()?->due_date}" . PHP_EOL;

        if ($count) {
          Invoice::destroy($postExpirationInvoices->pluck('id'));
          $generatedInvoiceDates = $expiredRent->rentInvoices()->select(['id', 'due_date'])->pluck('due_date')->all();

          $expiredRent->update([
            'status' => Rent::STATUS_EXPIRED,
            'next_invoice_date' => null,
            'generated_invoice_dates' => $generatedInvoiceDates
          ]);

          activity()
          ->causedBy($expiredRent)
          ->log("System removed {$count} invoices generated after {$expiredRent->end_date}");
        }
      }
    }
}
