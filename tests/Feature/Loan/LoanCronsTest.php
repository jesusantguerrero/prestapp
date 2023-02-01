<?php

namespace Tests\Feature\Loan;

use Tests\Feature\Loan\Helpers\LoanBase;

class LoanCronsTest extends LoanBase {

  public function testItGeneratesLateFees() {
    $loan = $this->createLoan([
      "date" => '2022-12-15',
      "amount" => 10000,
      "interest_rate" => 10,
      "frequency" => "MONTHLY",
      "repayment_count" => 4
    ]);

    $this->artisan('background:generate-loan-fees')->assertSuccessful();
    $repayment = $loan->installments()->late()->first();
    $this->assertGreaterThan(0, $loan->hasLateInstallments());
    $this->assertGreaterThan(0, $repayment->late_fee);
  }
}
