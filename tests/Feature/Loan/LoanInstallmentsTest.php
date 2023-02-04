<?php

namespace Tests\Feature\Loan;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use App\Models\User;
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

    $amount = 157;
    $response = $this->payRepayment($repayment, $loan, $amount);
    $repayment = $repayment->refresh();

    $response->assertStatus(200);
    $this->assertEquals($amount, $repayment->refresh()->amount_paid);
    $this->assertGreaterThan(0, $repayment->refresh()->amount_due);
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

  public function testItShouldDeleteAPayment() {
    $loan = $this->createLoan();
    $repayment = $loan->installments->first();

    $this->payRepayment($repayment, $loan, $repayment->amount_due);
    $payment = $loan->refresh()->paymentDocuments->first();
    $this->delete("/loans/$loan->id/payments/$payment->id");


    $this->assertEquals(0, $loan->paymentDocuments()->count());
    $this->assertGreaterThan(0, $repayment->amount_due);
  }

  public function testPaymentShouldNotDeletedByOtherUser() {
    $loan = $this->createLoan();
    $repayment = $loan->installments->first();
    $user = User::factory()->withPersonalTeam()->create();

    $this->payRepayment($repayment, $loan, $repayment->amount_due);
    $payment = $loan->refresh()->paymentDocuments->first();
    $this->actingAs($user);
    $response = $this->delete("/loans/$loan->id/payments/$payment->id");


    $response->assertForbidden();
  }

  public function testItShouldEditRepaymentInterest() {
    $loan = $this->createLoan();
    $repayment = $loan->installments->first();

    $response = $this->put("/loans/$loan->id/installments/$repayment->id", [
      "interest" => 20
    ]);

    $repayment = $repayment->refresh();

    $response->assertStatus(200);
    $this->assertEquals(20, 20);
  }

  public function testItShouldNotEditCapital() {
    $loan = $this->createLoan();
    $repayment = $loan->installments->first();

    $response = $this->payRepayment($repayment, $loan, $repayment->amount_due);

    $repayment = $repayment->refresh();

    $response->assertStatus(200);
    $this->assertEquals(0, $repayment->refresh()->amount_due);
    $this->assertEquals(LoanInstallment::STATUS_PAID, $repayment->payment_status);
  }
}
