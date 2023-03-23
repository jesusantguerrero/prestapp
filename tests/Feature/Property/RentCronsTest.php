<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Actions\GenerateInvoices;
use App\Domains\Properties\Models\Rent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;
use Tests\Feature\Property\Helpers\PropertyBase;

class RentCronsTest extends PropertyBase
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

    $createdDate = now()->subMonths(2)->format('Y-m-d');
    $firstInvoiceDate = InvoiceHelper::getNextDate($createdDate);
    $rent = $this->createRent([
      "date" => $createdDate,
      "amount" => 5000,
      "first_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
      "next_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
    ]);

    GenerateInvoices::chargeLateFees(true);
    $this->assertCount(1, $rent->rentInvoices);
    $this->assertEquals($rent->rentInvoices->first()->status, Invoice::STATUS_OVERDUE);
    $this->assertCount(1, $rent->lateFeeInvoices);
    $this->assertEquals($rent->fresh()->status, Rent::STATUS_LATE);
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
