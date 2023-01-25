<?php

namespace Tests\Feature\Loan;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Insane\Journal\Models\Core\Account;
use Tests\Feature\Loan\Helpers\LoanHelper;
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

    $this->loanData = LoanHelper::getData($this->lender);
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
