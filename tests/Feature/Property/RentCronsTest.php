<?php

namespace Tests\Feature\Property;

use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Properties\Models\Property;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Property\Helpers\PropertyBase;
use App\Domains\Accounting\Helpers\InvoiceHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Properties\Actions\GenerateInvoices;

class RentCronsTest extends PropertyBase
{
  use WithFaker;
  use RefreshDatabase;

  public function makePropertyExpense(Property $property) {
   return $this->post("/properties/{$property->id}/transactions/expense", array_merge($this->rentData, [
      'client_id' => $property->owner_id,
      'account_id' => Account::guessAccount($property, ['Property Expenses', 'expenses']),
      'amount' => 1000,
      'date' => '2023-01-30',
      'details' => 'Fix front door',
      'concept' => 'fix front door',
    ]));
  }

  public function testItGenerateLateFees() {
    $this->actingAs($this->user);

    $createdDate = now()->subRealMonths(2)->format('Y-m-d');
    $firstInvoiceDate = InvoiceHelper::getNextDate($createdDate);
    $rent = $this->createRent([
      "date" => $createdDate,
      "amount" => 5000,
      "first_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
      "next_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
    ]);

    GenerateInvoices::chargeLateFees(true);
    $this->assertCount(2, $rent->rentInvoices);
    $this->assertEquals($rent->rentInvoices->first()->status, Invoice::STATUS_OVERDUE);
    $this->assertCount(1, $rent->lateFeeInvoices);
    $this->assertEquals($rent->fresh()->status, Rent::STATUS_LATE);
  }

  public function testItShouldGenerateScheduledInvoices() {
    $this->actingAs($this->user);

    $createdDate = now()->subRealMonths(2)->format('Y-m-d');
    $firstInvoiceDate = InvoiceHelper::getNextDate($createdDate);
    $rent = $this->createRent([
      "date" => $createdDate,
      "amount" => 5000,
      "first_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
      "next_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
    ]);

    GenerateInvoices::scheduledRents();
    $this->assertCount(3, $rent->rentInvoices);
  }

}
