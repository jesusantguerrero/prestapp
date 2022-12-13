<?php

namespace App\Domains\Properties\Actions;

use App\Domains\Loans\Models\Loan;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyService;
use Exception;
use Illuminate\Support\Carbon;

class UpdateLateInvoices {

    public static function generateInvoices() {
      $rentWithInvoicesToCreate = Rent::whereRaw('next_invoice_date <= curdate()')
      ->whereNotIn('status', [Rent::STATUS_CANCELLED, Rent::STATUS_PAID])
      ->get();

      foreach ($rentWithInvoicesToCreate as $rent) {
        if (!in_array($rent->next_invoice_date, $rent->generated_invoice_dates))  {
          PropertyService::createInvoice([
            'date' => $rent->next_invoice_date
          ], $rent);
          $rent->update([
            'next_invoice_date' => self::getNextDate($rent->next_invoice_date),
            'generated_invoice_dates' => array_merge()
          ]);
        }
          }
    }

    public static function chargeLateFee($payments) {
        foreach ($payments as $payment) {
            $penaltyAmount = 0;

            if ($payment->loan->penalty_type == 'PERCENTAGE') {
                $penaltyAmount = ($payment->loan->penalty / 100) * $payment->amount_due;
            } else {
                $penaltyAmount = $payment->loan->penalty;
            }

            $payment->update([
                'penalty' => $penaltyAmount,
                'amount' => $payment->amount + $penaltyAmount
            ]);

            $payment->loan->update([
                'payment_status' => Loan::STATUS_LATE
            ]);

            $payment->loan->client->checkStatus();
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
