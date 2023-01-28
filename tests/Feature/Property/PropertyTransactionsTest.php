<?php

namespace Tests\Feature\Property;

use App\Domains\Properties\Models\Rent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;
use Tests\Feature\Property\Helpers\PropertyBase;

class PropertyTransactionsTest extends PropertyBase
{
  use WithFaker;
  use RefreshDatabase;

  public function testItShouldCreateAPropertyExpense() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $response = $this->post("/properties/{$rent->id}/transactions/expense", array_merge($this->rentData, [
      'client_id' => $rent->client_id,
      'account_id' => Account::guessAccount($rent, ['Property Expenses', 'expenses']),
      'amount' => 1000,
      'date' => '2023-01-30',
      'details' => 'Fix front door',
      'concept' => 'fix front door',
    ]));
    $response->assertStatus(200);
    $rent = Rent::first();

    $this->assertCount(1, $rent->rentExpenses);
    $this->assertEquals(1000, $rent->rentExpenses[0]->total);
    $this->assertEquals(Invoice::STATUS_UNPAID, $rent->rentExpenses[0]->status);
  }
}
