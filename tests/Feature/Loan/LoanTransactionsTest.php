<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Insane\Journal\Models\Core\Account;
use Tests\TestCase;

class LoanTest extends TestCase
{
  use WithFaker;
  use RefreshDatabase;

  private User $user;
  private Client $lender;
  private mixed $loanData;

  protected function setup(): void {
    parent::setup();
    $user = User::factory()->withPersonalTeam()->create();
    $user->current_team_id = $user->fresh()->ownedTeams()->latest('id')->first()->id;
    $user->save();
    $this->user = $user;
    $this->lender = Client::factory()->create([
      'user_id' => $this->user->id,
      'team_id' => $this->user->current_team_id,
    ]);

    $date = date('Y-m-d');
    $firstRepaymentDate = InvoiceHelper::getNextDate($date);

    $this->loanData = [
      'client_id' => $this->lender->id,
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
      'source_account_id' => Account::guessAccount($this->lender, ['daily_box']),
      'installments' => [[
        "installment_number" => 1,
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
        "installment_number" => 2,
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
        "installment_number" => 3,
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
        "installment_number" => 4,
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

  private function createLoan() {
    $this->actingAs($this->user);
    $this->post('/loans', $this->loanData);

    return Loan::latest()->first();
  }

  /**
   * A basic feature test example.
   *
   * @return void
   */
  public function testItShouldGetLoans()
  {
      $this->actingAs($this->user);

      $response = $this->get('/loans');

      $response->assertStatus(200);
  }

  public function testItShouldCreateLoan() {
    $this->actingAs($this->user);

    $response = $this->post('/loans', $this->loanData);
    $response->assertStatus(302);
    $this->assertCount(1, Loan::all());
  }

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
