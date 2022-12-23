<?php

namespace App\Domains\Properties\Actions;

use App\Domains\Loans\Models\Loan;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Services\RentService;
use Exception;
use Illuminate\Support\Carbon;
use Insane\Journal\Models\Invoice\Invoice;

class GenerateInvoices {

    public static function scheduledRents() {
      $rentWithInvoicesToCreate = Rent::whereRaw('next_invoice_date - curdate() <= 31')
      ->whereNotIn('status', [Rent::STATUS_CANCELLED, Rent::STATUS_PAID])
      ->get();

      foreach ($rentWithInvoicesToCreate as $rent) {
        if (!in_array($rent->next_invoice_date, $rent->generated_invoice_dates))  {
          RentService::createInvoice([
            'date' => $rent->next_invoice_date
          ], $rent);
          $rent->update([
            'next_invoice_date' => self::getNextDate($rent->next_invoice_date),
            'generated_invoice_dates' => array_merge()
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
          RentService::createInvoice([
            'date' => $rent->next_invoice_date
          ], $rent);
          $rent->update([
            'next_invoice_date' => self::getNextDate($rent->next_invoice_date),
            'generated_invoice_dates' => array_merge()
          ]);
        }
      }
    }

    public static function chargeLateFees() {
      $lateInvoices = Invoice::select(['invoices.*','rents.id as rentId', 'rents.grace_days as rentGraceDays'])->whereRaw('debt > 0 AND DATE_ADD(due_date, INTERVAL COALESCE(rents.grace_days, 0) DAY) < curdate()')
      ->join('rents', 'invoiceable_id', 'rents.id')
      ->where('invoiceable_type', Rent::class)
      ->whereNot('invoices.status', 'overdue')
      ->get();
      if (count($lateInvoices)) {
          self::chargeLateFee($lateInvoices);
      }
  }

    public static function chargeLateFee($invoices) {
      foreach ($invoices as $invoice) {
          $penaltyAmount = 0;

          if ($invoice->invoiceable->late_fee_type == 'PERCENTAGE') {
              $penaltyAmount = ($invoice->invoiceable->late_fee / 100) * $invoice->invoiceable->total;
          } else if ($invoice->invoiceable->late_fee_type == 'PERCENTAGE_OUTSTANDING') {
              $penaltyAmount = $invoice->invoiceable->debt;
          } else {
              $penaltyAmount = $invoice->invoiceable->late_fee;
          }         

          $invoice->update([
            'status' => 'overdue'
          ]);

          $invoice->invoiceable->update([
            'status' => Rent::STATUS_LATE
          ]);

          RentService::createInvoice([
            "name" => "Factura de mora",
            "concept" => "Factura de mora {$invoice->invoiceable->client->fullName}",
            'invoice_account_id' => $invoice->invoiceable->commission_account_id,
            'total' => $penaltyAmount
          ], $invoice->invoiceable);

          $invoice->invoiceable->client->checkStatus();
      }
    }

    public static function getNextDate($isoDate) {
        $date = Carbon::createFromFormat('Y-m-d', $isoDate);
        $month = $date->format('m');
        return $month == '01' ?  self::getForFebruary($date) : $date->addMonths(1);
    }

    public static function getForFebruary($date){
      $year = $date->format('Y');
      $month = '02';
      $day = $date->format('d');
      if($day > 28) $day = '28';
      $newDate = "$year-$month-$day";
    
      return Carbon::createFromFormat('Y-m-d', $newDate);
    }
}
