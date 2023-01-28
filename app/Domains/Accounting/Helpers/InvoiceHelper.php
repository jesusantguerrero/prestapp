<?php

namespace App\Domains\Accounting\Helpers;

use Illuminate\Support\Carbon;

class InvoiceHelper {

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

    public static function getNextScheduleDate($isoDate, $frequency) {
      $date = Carbon::createFromFormat('Y-m-d', $isoDate);
      $methods = [
        "WEEKLY" => [
            "method" => "addDays",
            "interval" => 7
        ],
        "BIWEEKLY" => [
            "method" => "addDays",
            "interval" => 15
        ],
        "SEMIMONTHLY" => [
            "method" => "addSemiMonth",
            "interval" => 1
        ],
        "MONTHLY" => [
            "method" => "addMonths",
            "interval" => 1
        ]
      ];

      ["method" => $method, "interval" => $interval] = $methods[$frequency];
      return $date->$method($interval)->format('Y-m-d');
    }

    public static function numberToWords($number) {
      $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
      return $formatter->format($number) . "\n";
    }
}
