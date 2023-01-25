<?php

namespace Tests\Unit\Loan;

use App\Domains\Loans\Helpers\RepaymentSchedule;
use PHPUnit\Framework\TestCase;

class RepaymentScheduleTest extends TestCase
{
    protected $loanSchedule;

    protected function setUp(): void
    {
        parent::setUp();
        $date = '2022-01-15';
        $this->loanSchedule = new RepaymentSchedule([
            "startDate" => $date, 
            "frequency" => 'MONTHLY',
            "capital" => 20000, 
            "interestMonthlyRate" => 20, 
            "count" => 12
        ]);
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testItCalculateMonthlyPayment()
    {
        
        $this->assertEquals(4505.30, $this->loanSchedule->payment);
    }

    public function testItCalculatesInstallments() {
      $firstPayment = $this->loanSchedule->getInstallment(1);
      $this->assertEquals(20000, $firstPayment->initial_balance);
      $this->assertEquals(4000, $firstPayment->interest);
      $this->assertEquals(505.3, $firstPayment->principal);

      $lastPayment = $this->loanSchedule->getInstallment(12);
      $this->assertEquals($lastPayment->initial_balance, 3754.38);
      $this->assertEquals(750.88, $lastPayment->interest);
      $this->assertEquals(3754.42, $lastPayment->principal);
      $this->assertTrue($lastPayment->final_balance <= 0);
    }

    
    public function testItCalculatesMonthlyDates() {
      $firstPayment = $this->loanSchedule->getInstallment(1);
      $this->assertEquals($firstPayment->due_date, '2022-01-15');

      $payment2 = $this->loanSchedule->getInstallment(2);
      $this->assertEquals($payment2->due_date, '2022-02-15');
      
      $payment12 = $this->loanSchedule->getInstallment(12);
      $this->assertEquals($payment12->due_date, '2022-12-15');
  }
}
