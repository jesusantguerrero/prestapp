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

  public function testItShouldHaveDepositBalance() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent([
      "deposit" => 5000,
      "price" => 5000,
      'is_deposit_received' => true,
    ]);

    $this->assertEquals($rent->deposit, $rent->client->depositBalance());
  }

  public function testItShouldNotRefundMoreThanOwed() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent([
      "deposit" => 5000,
      "price" => 5000
    ]);

    $response = $this->post("/properties/{$rent->id}/transactions/refund", [
      'client_id' => $rent->client_id,
      'account_id' => $rent->property->deposit_account_id,
      'total' => 6000,
      'rent_id' => $rent->id,
      'payments' => [[
        'amount' => 6000,
        'original_amount' => $rent->deposit,
        'rent_id' => $rent->id,
        'id' => '1'
      ]],
    ]);

    $response->assertStatus(200);
    $response->assertSessionHasErrors();
  }

  public function testItShouldCreateADepositRefund() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent([
      "deposit" => 5000,
      "price" => 5000,
      'is_deposit_received' => true,
      'payment_method' => 'cash'
    ]);

    $response = $this->post("/properties/{$rent->id}/transactions/refund", [
      'client_id' => $rent->client_id,
      'account_id' => $rent->property->deposit_account_id,
      'total' => $rent->deposit,
      'rent_id' => $rent->id,
      'payments' => [[
        'amount' => $rent->deposit,
        'original_amount' => $rent->deposit,
        'rent_id' => $rent->id,
        'id' => '1'
      ]],
    ]);

    $response->assertStatus(200);
    $this->assertEquals($rent->refunds()->first()->total, 5000);
    $this->assertEquals($rent->refunds()->first()->status, Invoice::STATUS_UNPAID);

  }

  public function testItShouldCreateADepositRefundPaid() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent([
      "deposit" => 5000,
      "price" => 5000
    ]);

    $response = $this->post("/properties/{$rent->id}/transactions/refund", [
      'client_id' => $rent->client_id,
      'account_id' => $rent->property->deposit_account_id,
      'total' => $rent->deposit,
      'rent_id' => $rent->id,
      'payments' => [[
        'amount' => $rent->deposit,
        'original_amount' => $rent->deposit,
        'rent_id' => $rent->id,
        'id' => '1'
      ]],
      'payment_details' => [
        'account_id' => Account::guessAccount($rent, ['Property Expenses', 'expenses']),
        'details' => 'A custom note',
        'payment_method' => 'cash'
      ]
    ]);

    $response->assertStatus(200);
    $this->assertEquals($rent->refunds()->first()->status, Invoice::STATUS_PAID);
  }
}
