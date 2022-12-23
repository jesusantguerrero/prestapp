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
}
