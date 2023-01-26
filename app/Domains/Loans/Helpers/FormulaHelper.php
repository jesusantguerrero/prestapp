<?php

namespace App\Domains\Loans\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FormulaHelper {
    const ROUNDING = 2;

    public static function fixedRepaymentAmount($loanBalance, $interestRate, $repaymentCount){
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $decimals = self::ROUNDING;
      $sheet->setCellValue('A1', "=ROUND(ABS(PMT($interestRate, $repaymentCount, $loanBalance)), $decimals)");
      return (double) $sheet->getCell('A1')->getFormattedValue();
    }


    public static function subWithRounding($num1, $num2) {
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $decimals = self::ROUNDING;
      $sheet->setCellValue('A1', "=ROUND(SUM($num1, -$num2), $decimals)");
      return (double) $sheet->getCell('A1')->getFormattedValue();
    }

    public static function mulWithRounding($num1, $num2) {
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $decimals = self::ROUNDING;
      $sheet->setCellValue('A1', "=ROUND($num1 * $num2, $decimals)");
      return (double) $sheet->getCell('A1')->getFormattedValue();
    }
}
