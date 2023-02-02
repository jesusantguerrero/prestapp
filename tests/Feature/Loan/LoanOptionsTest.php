<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
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
    // #TODO Allow refinance a loan
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
