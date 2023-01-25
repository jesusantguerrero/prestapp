<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Loans\Models\Loan;
use Tests\Feature\Loan\Helpers\LoanBase;

class LoanTransactionsTest extends LoanBase {

  public function testItShouldCreateLoanAgreement() {
    $loan = $this->createLoan();

    $response = $this->post("/loans/$loan->id/agreements", [
      'date' => date('Y-m-d'),
      'reason' => 'No tiene dinero',
      'due_date' => InvoiceHelper::getNextDate(date('Y-m-d'))
    ]);

    $response->assertStatus(200);
    $this->assertCount(1, $loan->agreements);
    $this->assertEquals($loan->agreements->first()->total, 1390.64);
    $this->assertEquals(date('Y-m-d'), $loan->fresh()->cancelled_at);
    $this->assertEquals(Loan::STATUS_CANCELLED, $loan->fresh()->payment_status);
  }
}
