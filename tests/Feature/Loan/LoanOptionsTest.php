<?php

namespace Tests\Feature\Loan;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Loans\Helpers\RepaymentSchedule;
use App\Domains\Loans\Models\Loan;
use Insane\Journal\Models\Core\Account;
use Tests\Feature\Loan\Helpers\LoanBase;

class LoanOptionsTest extends LoanBase
{
  public function testItShouldPayoff() {
    $loan = $this->createLoan();

    $response = $this->post("/loans/$loan->id/payoff", [
      'date' => date('Y-m-d'),
      'account_id' => Account::guessAccount($loan, ['daily_box']),
    ]);

    $response->assertStatus(200);
    $this->assertEquals(Loan::STATUS_PAID, $loan->fresh()->payment_status);
  }

  public function testItShouldPayoffWithAmount() {
    $loan = $this->createLoan();

    $response = $this->post("/loans/$loan->id/payoff", [
      'date' => date('Y-m-d'),
      'due_date' => InvoiceHelper::getNextDate(date('Y-m-d')),
      'interest' => 800,
      'principal' => 200,
    ]);

    $response->assertStatus(200);
    $this->assertEquals(Loan::STATUS_PAID, $loan->fresh()->payment_status);
  }

  public function testItShouldRefinanceLoan() {
    $loan = $this->createLoan([
      'amount' => 5000
    ]);

    $firstPaymentDate = date('Y-m-d');
    $loanData = [
      'installment_agreement' => false,
      'compulsory' => false,
      'first_repayment_date' => date('Y-m-d'),
      'date' => date('Y-m-d'),
      'due_date' => InvoiceHelper::getNextDate(date('Y-m-d')),
      'amount' => 4000,
      'interest_rate' => 10,
      'frequency' => 'MONTHLY',
      'repayment_count' => 2
    ];

    $schedule = new RepaymentSchedule([
      "startDate" => $firstPaymentDate,
      "frequency" => $loanData['frequency'],
      "capital" => $loanData['amount'],
      "interestMonthlyRate" => $loanData['interest_rate'],
      "count" => $loanData['repayment_count']
    ]);

    $loanData['installments'] = array_map(function($repayment) {
      return (array) $repayment;
    }, $schedule->payments);

    $response = $this->post("/loans/$loan->id/refinance", $loanData);

    $agreement = Loan::refinance()->first();

    $response->assertStatus(200);
    $this->assertEquals(Loan::STATUS_CANCELLED, $loan->fresh()->payment_status);
    $this->assertEquals(Loan::STATUS_PENDING, $agreement->payment_status);
  }

  public function testItShouldWriteOffLoan() {
    // #TODO Allow write off a loan
  }

  public function testItShouldCloseLoan() {
    $loan = $this->createLoan();

    $response = $this->post("/loans/$loan->id/close", [
      'date' => date('Y-m-d'),
      'reason' => 'Misericordia divina',
    ]);

    $response->assertStatus(200);
    $this->assertEquals(Loan::STATUS_CANCELLED, $loan->fresh()->payment_status);
    $this->assertEquals('closed', $loan->fresh()->cancel_type);
  }

  public function testItShouldRescheduleLoan(){
     // #TODO Allow to reschedule a loan
  }
}
