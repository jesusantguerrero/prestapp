<?php

namespace Tests\Feature\Loan\Helpers;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Helpers\RepaymentSchedule;
use Insane\Journal\Models\Core\Account;

class LoanHelper {

  public static function getData(Client $lender, $formData = []) {
    $date = $formData['date'] ?? date('Y-m-d');
    $firstRepaymentDate = InvoiceHelper::getNextDate($date);

    $loanData = [
      'client_id' => $lender->id,
      'amount' => $formData['amount'] ?? 1000,
      'interest_rate' => $formData['interest_rate'] ?? 10,
      'repayment_count' => $formData['repayment_count'] ?? 4,
      'date' => $date,
      'disbursement_date' => $date,
      'fist_installment_date' => $firstRepaymentDate,
      'frequency' => $formData['frequency'] ?? 'MONTHLY',
      'grace_days' => 5,
      'interest_rate' => 10,
      'late_fee' => 10,
      'paid_installments' => 0,
      'closing_fee' => 0,
      'source_type' => 'DAILY_BOX',
      'source_account_id' => Account::guessAccount($lender, ['daily_box']),
    ];

    $schedule = new RepaymentSchedule([
      "startDate" => $date,
      "frequency" => $loanData['frequency'],
      "capital" => $loanData['amount'],
      "interestMonthlyRate" => $loanData['interest_rate'],
      "count" => $loanData['repayment_count']
    ]);

    $loanData['installments'] = array_map(function($repayment) {
      return (array) $repayment;
    }, $schedule->payments);

    return $loanData;
  }
}
