<?php

namespace Tests\Feature\Loan\Helpers;

use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Loan\Helpers\LoanHelper;
use Tests\TestCase;

abstract class LoanBase extends TestCase
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

  public function createLoan() {
    $this->actingAs($this->user);
    $this->post('/loans', $this->loanData);

    return Loan::latest()->first();
  }
}