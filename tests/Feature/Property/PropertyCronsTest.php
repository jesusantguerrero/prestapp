<?php

namespace Tests\Feature\Property;

use App\Domains\Properties\Models\Rent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;
use Tests\Feature\Property\Helpers\PropertyBase;

class PropertyCronsTest extends PropertyBase
{
  use WithFaker;
  use RefreshDatabase;

  public function makePropertyExpense($rent) {
   return $this->post("/properties/{$rent->id}/transactions/expense", array_merge($this->rentData, [
      'client_id' => $rent->client_id,
      'account_id' => Account::guessAccount($rent, ['Property Expenses', 'expenses']),
      'amount' => 1000,
      'date' => '2023-01-30',
      'details' => 'Fix front door',
      'concept' => 'fix front door',
    ]));
  }

  public function testItGenerateLateFees() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $response = $this->makePropertyExpense($rent);

    $response->assertStatus(200);
    $rent = Rent::first();

    $this->assertCount(1, $rent->rentExpenses);
    $this->assertEquals(1000, $rent->rentExpenses[0]->total);
    $this->assertEquals(Invoice::STATUS_UNPAID, $rent->rentExpenses[0]->status);
  }

  public function testItShouldGenerateTodaysPayments() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $this->makePropertyExpense($rent);
    $expense = $rent->rentExpenses->first();
    $transaction = $expense->transaction;

    $this->assertEquals(1000, $transaction->total);
    $this->assertEquals($expense->account_id, $transaction->lines[0]->account_id);
    $this->assertEquals(1, $transaction->lines[0]->type);
    $this->assertEquals($expense->invoice_account_id, $transaction->lines[1]->account_id);
    $this->assertEquals(-1, $transaction->lines[1]->type);
  }

}
