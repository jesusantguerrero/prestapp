<?php

namespace App\Domains\Loans\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FormulaHelper {
    public static function fixedRepaymentAmount($loanBalance, $interestRate, $repaymentCount){
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setCellValue('A1', "=ROUND(ABS(PMT($interestRate, $repaymentCount, $loanBalance)), 2)");
      return (double) $sheet->getCell('A1')->getFormattedValue();
    }


    public static function subWithRounding($num1, $num2) {
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setCellValue('A1', "=ROUND(SUM($num1, -$num2), 2)");
      return (double) $sheet->getCell('A1')->getFormattedValue();
    }

    public static function mulWithRounding($num1, $num2) {
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setCellValue('A1', "=ROUND($num1 * $num2, 2)");
      return (double) $sheet->getCell('A1')->getFormattedValue();
    }
}
