<?php

namespace Tests\Feature\Loan\Helpers;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use Insane\Journal\Models\Core\Account;

class LoanHelper {

  public static function getData(Client $lender) {
    $date = date('Y-m-d');
    $firstRepaymentDate = InvoiceHelper::getNextDate($date);

    return [
      'client_id' => $lender->id,
      'amount' => 1000,
      'interest_rate' => 10,
      'repayment_count' => 4,
      'date' => $date,
      'disbursement_date' => $date,
      'fist_installment_date' => $firstRepaymentDate,
      'frequency' => 'MONTHLY',
      'grace_days' => 5,
      'interest_rate' => 10,
      'paid_installments' => 0,
      'closing_fee' => 0,
      'source_type' => 'DAILY_BOX',
      'source_account_id' => Account::guessAccount($lender, ['daily_box']),
      'installments' => [[
        "number" => 1,
        "due_date" => "2023-02-19",
        "days" => 0,
        "amount" => "347.66",
        "principal" => "167.66",
        "interest" => "180.00",
        "fees" => "0.00",
        "late_fee" => "0.00",
        "initial_balance" => "900.00",
        "final_balance" => "732.34",
      ], [
        "number" => 2,
        "due_date" => "2023-03-19",
        "days" => 0,
        "amount" => "347.66",
        "amount_due" => "0.00",
        "principal" => "201.19",
        "interest" => "146.47",
        "fees" => "0.00",
        "late_fee" => "0.00",
        "initial_balance" => "732.34",
        "final_balance" => "531.15",
      ], [
        "number" => 3,
        "due_date" => "2023-04-19",
        "days" => 0,
        "amount" => "347.66",
        "amount_due" => "347.66",
        "principal" => "241.43",
        "interest" => "106.23",
        "fees" => "0.00",
        "late_fee" => "0.00",
        "initial_balance" => "531.15",
        "final_balance" => "289.72",
      ], [
        "number" => 4,
        "due_date" => "2023-05-19",
        "days" => 0,
        "amount" => "347.66",
        "amount_due" => "347.66",
        "principal" => "289.72",
        "interest" => "57.94",
        "fees" => "0.00",
        "late_fee" => "0.00",
        "initial_balance" => "289.72",
        "final_balance" => "0.00",
        ]
      ]
    ];
  }
}
