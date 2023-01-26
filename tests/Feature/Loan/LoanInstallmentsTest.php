<?php

namespace Tests\Feature\Property;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use Insane\Journal\Models\Core\Account;
use Tests\Feature\Loan\Helpers\LoanBase;

class LoanInstallmentsTest extends LoanBase {

  public function payRepayment(LoanInstallment $repayment, Loan $loan, $paymentAmount) {
    return $this->post("/loans/$loan->id/installments/$repayment->id/pay", [
      'client_id' => $loan->client_id,
      'account_id' => Account::findByDisplayId('daily_box', $loan->team_id),
      'amount' => $paymentAmount,
      'date' => date('Y-m-d'),
      'details' => 'First payment',
      'concept' => 'First payment',
    ]);
  }

  public function testItShouldPayARepayment() {
    $loan = $this->createLoan();
    $repayment = $loan->installments->first();

    $response = $this->payRepayment($repayment, $loan, $repayment->amount_due);

    $repayment = $repayment->refresh();

    $response->assertStatus(200);
    $this->assertEquals(0, $repayment->refresh()->amount_due);
    $this->assertEquals(LoanInstallment::STATUS_PAID, $repayment->payment_status);
  }

  public function testItShouldPayARepaymentPartially() {
    $loan = $this->createLoan();
    $repayment = $loan->installments->first();

    $amount = $repayment->amount_due / 2;
    $response = $this->payRepayment($repayment, $loan, $amount);
    $repayment = $repayment->refresh();

    $response->assertStatus(200);
    $this->assertEquals($amount, $repayment->refresh()->amount_due);
    $this->assertEquals(LoanInstallment::STATUS_PARTIALLY_PAID, $repayment->payment_status);
  }

  public function testItShouldErrorIfPayMore() {
    $loan = $this->createLoan();
    $repayment = $loan->installments->first();

    $amount = $repayment->amount_due + 20;
    $response = $this->payRepayment($repayment, $loan, $amount);

    $repayment = $repayment->refresh();
    $response->assertStatus(404);
  }
}
