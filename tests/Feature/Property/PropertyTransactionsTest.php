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

    $response = $this->createExpense($rent);

    $response->assertStatus(200);
    $rent = Rent::first();

    $this->assertCount(1, $rent->rentExpenses);
    $this->assertEquals(1000, $rent->rentExpenses[0]->total);
    $this->assertEquals(Invoice::STATUS_UNPAID, $rent->rentExpenses[0]->status);
  }

  public function testPropertyExpenseShouldBeAccountingRight() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $this->createExpense($rent);
    $expense = $rent->rentExpenses->first();
    $transaction = $expense->transaction;

    $this->assertEquals(1000, $transaction->total);
    $this->assertEquals($expense->account_id, $transaction->lines[0]->account_id);
    $this->assertEquals(1, $transaction->lines[0]->type);
    $this->assertEquals($expense->invoice_account_id, $transaction->lines[1]->account_id);
    $this->assertEquals(-1, $transaction->lines[1]->type);
  }

  public function testShouldPayPropertyExpense() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent();
    $account = Account::findByDisplayId('daily_box', $rent->team_id);

    $this->createExpense($rent);
    $expense = $rent->rentExpenses->first();
    $this->payInvoice($rent, $expense, [
      'account_id' => $account->id,
    ]);
    $payment = $rent->rentExpenses->first()->payments->first();

    $this->assertEquals(1000, $payment->amount);
    $this->assertEquals(1, $payment->transaction->lines[0]->type);
    $this->assertEquals($expense->invoiceAccount->display_id, $payment->transaction->lines[0]->account->display_id);
    $this->assertEquals($account->id, $payment->transaction->lines[1]->account_id);
    $this->assertEquals(-1, $payment->transaction->lines[1]->type);
  }
}
