<?php

namespace App\Domains\Loans\Services;

use App\Domains\Loans\Jobs\CreateLoanTransaction;
use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;

class LoanInstallmentService {
    public static function updateValues(mixed $loanData, LoanInstallment $loanInstallment) {
        $fields = [
          "interest" => [
            "condition" => function( $repayment, $data) {
              return $repayment->interest_paid >= $data['interest'];
            }
          ]
        ];

        $values = [];

        foreach ($fields as $fieldName => $field) {
          [ "condition" => $condition ] = $field;
          if ($condition($loanInstallment, $loanData)) {
            $values[$fieldName] = $loanData[$fieldName];
          }
        }

        $loanInstallment->update($values);
        return $loanInstallment;
    }
}
