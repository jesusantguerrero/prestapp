<?php

namespace App\Domains\Properties\Services;

use Illuminate\Support\Facades\DB;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;

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
      $nextDate = $rent->next_invoice_date ?? $rent->rentInvoices()->latest('due_date')->first()->due_date;
      $generatedInvoices = [];

      echo "rent of $rent->client_name will be updated until $dateTarget: $rent->end_date" . PHP_EOL;

      while ($nextDate && InvoiceHelper::getYearMonth($nextDate) <= InvoiceHelper::getYearMonth($dateTarget)) {
        $markAsPaid = $areInvoicesPaid && (!$paidUntil || $paidUntil >= $nextDate);
        $invoiceData = [
          'date' => $nextDate,
          'is_paid' => (boolean) $markAsPaid
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

    public static function removeInvoicesOfCancelled($teamId) {
      $invoices = Invoice::where([
        'invoices.team_id' => $teamId,
        'invoices.type' => 'INVOICE',
        'invoiceable_type' => Rent::class
      ])
      ->select("invoices.*")
      ->whereRaw('DATE_FORMAT(invoices.due_date,  "%Y-%m") >= DATE_FORMAT(rents.move_out_at, "%Y-%m")')
      ->join('rents', 'rents.id', '=', 'invoices.invoiceable_id')
      ->get();

      foreach ($invoices as $invoice) {

          $description = "removing invoice {$invoice->description} post expiration from {$invoice?->due_date} because is after move out {$invoice->rent_move_out_at}";
          echo $description . PHP_EOL;
          Invoice::destroy($invoice->id);
          $rent = Rent::find($invoice->invoiceable_id);
          $generatedInvoiceDates = $rent->rentInvoices()->select(['id', 'due_date'])->pluck('due_date')->all();

          $rent->update([
            'next_invoice_date' => null,
            'generated_invoice_dates' => $generatedInvoiceDates
          ]);

          activity()
          ->causedBy($rent)
          ->log("System {$description}");
      }
    }

    public static function removeChargeInvoices($teamId) {
      $invoices = Invoice::where([
        'invoices.team_id' => $teamId,
        'invoices.type' => 'INVOICE',
        'invoiceable_type' => Rent::class,
        'category_type' => PropertyInvoiceTypes::LateFee,
      ])
      ->select("invoices.*")
      ->whereIn('invoices.status', [Invoice::STATUS_UNPAID, Invoice::STATUS_OVERDUE])
      ->join('rents', 'rents.id', '=', 'invoices.invoiceable_id')
      ->get();

      foreach ($invoices as $invoice) {
          $description = "removing invoice {$invoice->description} charge from {$invoice?->due_date} because charges are not automatic anymore {$invoice->rent_move_out_at}";
          echo $description . PHP_EOL;
          Invoice::destroy($invoice->id);
          $rent = Rent::find($invoice->invoiceable_id);

          activity()
          ->causedBy($rent)
          ->log("System {$description}");
      }
    }


    public static function removeOverContractedRentInvoice($teamId = null, $state = null) {
      $rent = Rent::whereRaw('DATEDIFF(next_invoice_date, now()) > 365')
      ->whereNotIn('status', [Rent::STATUS_CANCELLED])
      ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
      ->get();

      foreach ($rent as $rent) {
        $rent->invoices()->select('id')
        ->where([
          'invoices.type' => 'INVOICE',
          'invoiceable_type' => Rent::class,
        ])
        ->whereIn('invoices.status', [Invoice::STATUS_UNPAID, Invoice::STATUS_OVERDUE])
        ->whereRaw('DATEDIFF(due_date, now()) > 365')->get();

        foreach ($rent->invoices as $invoice) {
          $description = "removing invoice {$invoice->description} of {$invoice?->due_date} because too large";
          echo $description . PHP_EOL;
          Invoice::destroy($invoice->id);
          Invoice::destroy($invoice->id);
        }

        $generatedDates = $rent->invoices()->select('due_date')->orderBy('due_date')->get()->pluck('due_date');
        $rent->update([
          'next_invoice_date' => $generatedDates->last(),
          'generated_invoice_dates' => $generatedDates->all()
        ]);
      }
    }

    public static function getSoBackInvoices(int $teamId, $startDate = null, $endDate = null) {
        $today = now()->timezone('America/Santo_Domingo')->format('Y-m-d');

          $rentClients = DB::table('invoices')
          ->selectRaw('invoices.id id')
          // ->selectRaw('invoices.id, concat(clients.names," ", clients.id, " ", sum(invoices.debt)) contact,  group_concat(concat(invoices.due_date, "(", invoices.debt, ")")) ids')
            ->where([
              'invoices.team_id' => $teamId,
              'invoices.type' => 'INVOICE',
            ])
            ->where(fn ($q) => $q->where('invoiceable_type', Rent::class)->orWhereNull('invoiceable_type'))
            ->where('debt', '>', 0)
            ->when($endDate, fn ($q) => $q->where('due_date', '<=', $endDate))
            ->when($today, fn ($q) => $q->where('due_date', '<', $today))
            ->where('debt', '>', 0)
            ->where('total', '>', 0)
            ->orderByDesc('invoices.due_date')
            // ->groupBy('invoices.client_id')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->get();

        echo  count($rentClients) . " clients with back invoices" . PHP_EOL;
        foreach ($rentClients as $clientInvoice) {
          print_r($clientInvoice);
          Invoice::destroy($clientInvoice->id);
          // $description = "removing invoice {$invoice->client} of {$invoice?->due_date} because too large";
          echo PHP_EOL;
        }
        echo  count($rentClients) . " clients with back invoices" . PHP_EOL;

    }
}
