<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Loans\Models\Loan;
use Tests\Feature\Loan\Helpers\LoanBase;

class LoanTransactionsTest extends LoanBase {

  public function testItShouldCreateLoanAgreement() {
    $loan = $this->createLoan();
    $loanDebt = (double) $loan->amount_due;

    $response = $this->post("/loans/$loan->id/agreements", [
      'date' => date('Y-m-d'),
      'reason' => 'No tiene dinero',
      'due_date' => InvoiceHelper::getNextDate(date('Y-m-d'))
    ]);

    $response->assertStatus(200);
    $this->assertCount(1, $loan->agreements);
    $this->assertEquals($loanDebt, $loan->agreements->first()->total);
    $this->assertEquals(date('Y-m-d'), $loan->fresh()->cancelled_at);
    $this->assertEquals(Loan::STATUS_CANCELLED, $loan->fresh()->payment_status);
  }

  public function testItShouldCreateLoanAgreementWithLoss() {
    $loan = $this->createLoan();

    $response = $this->post("/loans/$loan->id/agreements", [
      'date' => date('Y-m-d'),
      'reason' => 'No tiene dinero',
      'due_date' => InvoiceHelper::getNextDate(date('Y-m-d')),
      'amount' => 500,
    ]);

    $response->assertStatus(200);
    $this->assertCount(1, $loan->agreements);
    $this->assertEquals(500, $loan->agreements->first()->total);
    $this->assertEquals(date('Y-m-d'), $loan->fresh()->cancelled_at);
    $this->assertEquals(Loan::STATUS_CANCELLED, $loan->fresh()->payment_status);
  }

}
