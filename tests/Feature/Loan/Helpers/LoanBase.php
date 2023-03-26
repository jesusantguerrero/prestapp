<?php

namespace Tests\Feature\Loan\Helpers;

use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Insane\Journal\Models\Core\Account;
use Tests\TestCase;

abstract class LoanBase extends TestCase
{
  use WithFaker;
  use RefreshDatabase;

  protected User $user;
  protected Client $lender;
  protected mixed $loanData;

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

  public function fundAccount(string $accountDisplayId, int $amount, $teamId) {
      Account::findByDisplayId($accountDisplayId, $teamId)->openBalance($amount);
  }

  public function createLoan(mixed $formData = []) {
    $this->actingAs($this->user);
    $loanData = LoanHelper::getData($this->lender, $formData);
    $account = Account::find($loanData['source_account_id']);

    $account->openBalance($loanData['amount']);

    $this->post('/loans?json=true', LoanHelper::getData($this->lender, $formData));

    return Loan::latest()->first();
  }
}
