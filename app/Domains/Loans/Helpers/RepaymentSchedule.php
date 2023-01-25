<?php

namespace App\Domains\Loans\Helpers;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use Brick\Math\RoundingMode;

class RepaymentSchedule {
    public $payment = 0;
    public $payments = [];
    public $totalDebt = 0;
    public $totalInterest = 0;
    public $totalCapital = 0;
    public $startDate;
    public $frequency;
    public $capital = 0;
    public $interestMonthlyRate = 0;
    public $count = 0;

    public function __construct($loanTerms) {
        $this->startDate = $loanTerms["startDate"];
        $this->capital = $loanTerms["capital"];
        $this->interestMonthlyRate = $loanTerms["interestMonthlyRate"] / 100.00;
        $this->count = $loanTerms["count"];
        $this->frequency = $loanTerms["frequency"];
        $this->payment = $this->calculatePayment();
        $this->generateAmortizationTable();
    } 

    private function calculatePayment() {
      $interestRate = $this->getFrequencyRate();
      return FormulaHelper::fixedRepaymentAmount($this->capital, $interestRate, $this->count);
    }

    private function getFrequencyRate() {
      $intervals = [
          "WEEKLY" => 4,
          "BIWEEKLY" => 2,
          "SEMIMONTHLY"  =>  2,
          "MONTHLY"  =>  1,
      ];
      return $this->interestMonthlyRate / $intervals[$this->frequency];
    }

    private function generateAmortizationTable() {
      $interest = 0;
      $monthlyPrincipal = 0;
      $balance  = $this->capital;
      $dueDate = $this->startDate;
      $interestRate = $this->getFrequencyRate();

      for ($index = 0; $index < $this->count; $index++) {
          $interest = FormulaHelper::mulWithRounding($balance, $interestRate);
          $monthlyPrincipal = FormulaHelper::subWithRounding($this->payment, $interest);
          $finalBalance = FormulaHelper::subWithRounding($balance, $monthlyPrincipal);
          
          $this->payments[] = (object) [
              "number" => $index + 1,
              "due_date" => $dueDate,
              "days" => 0,
              "amount" => $this->payment,
              "amount_paid" => 0,
              "amount_due" => $this->payment,
              "interest"=> $interest,
              "principal" => $monthlyPrincipal,
              "fees" => 0,
              "late_fee" => 0,
              "principal_paid" => 0,
              "interest_paid" => 0,
              "fees_paid" => 0,
              "penalty_paid" => 0,
              "initial_balance" => $balance,
              "final_balance" => $finalBalance
          ];

          $this->totalCapital += $monthlyPrincipal;
          $this->totalDebt += $this->payment;
          $this->totalInterest += $interest;
          $balance = $finalBalance;
          $dueDate = InvoiceHelper::getNextScheduleDate($dueDate, $this->frequency);
      }
    }

    public function getInstallment($installmentNumber) {
      return $this->payments[$installmentNumber - 1];
    }
}
