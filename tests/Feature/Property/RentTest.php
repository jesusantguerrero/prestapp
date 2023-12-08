<?php

namespace Tests\Feature\Property;

use App\Domains\CRM\Enums\ClientStatus;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Invoice\Invoice;
use Tests\Feature\Property\Helpers\PropertyBase;
use App\Domains\Properties\Services\RentTransactionService;

class RentTest extends PropertyBase
{

  public function testItShouldGetRents()
  {
      $this->actingAs($this->user);

      $response = $this->get('/rents');

      $response->assertStatus(200);
  }

  public function testItShouldCreateRent() {
    $this->seed();
    $this->actingAs($this->user);

    $response = $this->post('/rents', $this->rentData);
    $response->assertStatus(302);
    $this->assertCount(1, Rent::all());
  }

  public function testItShouldCreateDepositTransaction() {
    $this->seed();
    $this->actingAs($this->user);

    $response = $this->post('/rents', array_merge($this->rentData, [
      'deposit' => 12000,
      'amount' => 6000,
      'date' => '2023-01-15',
      'deposit_due' => '2023-01-15',
      'first_invoice_date' => '2023-01-30',
    ]));

    $response->assertStatus(302);
    $rent = Rent::first();
    $this->assertCount(1, $rent->depositInvoices);
    $this->assertEquals(12000, $rent->depositInvoices[0]->total);
  }

  public function testItShouldCreatePaidDepositInvoice() {
    $this->seed();
    $this->actingAs($this->user);

    $response = $this->post('/rents', array_merge($this->rentData, [
      'deposit' => 12000,
      'amount' => 6000,
      'is_deposit_received' => true,
      'payment_method_id' => 'cash',
      'date' => '2023-01-15',
      'deposit_due' => '2023-01-15',
      'first_invoice_date' => '2023-01-30',
    ]));

    $response->assertStatus(302);
    $rent = Rent::first();
    $this->assertEquals($rent->depositInvoices()->first()->status, Invoice::STATUS_PAID);
  }

  public function testItShouldCreateRentWithProratedAmount() {
    $this->seed();
    $this->actingAs($this->user);

    $response = $this->post('/rents', array_merge($this->rentData, [
      'deposit' => 12000,
      'amount' => 6000,
      'date' => '2023-01-15',
      'deposit_due' => '2023-01-15',
      'first_invoice_date' => '2023-01-30',
    ]));
    $response->assertStatus(302);
    $rent = Rent::first();
    $this->assertEquals(3000, $rent->rentInvoices[0]->total);
  }

  public function testItShouldCreateRentWithInvoicesGenerated() {
    $this->seed();
    $this->actingAs($this->user);

    $response = $this->post('/rents', array_merge($this->rentData, [
      'deposit' => 12000,
      'amount' => 6000,
      'date' => now()->subMonths(8)->format('Y-m-d'),
      'deposit_due' => now()->subMonths(8)->format('Y-m-d'),
      'first_invoice_date' => now()->subMonths(7)->format('Y-m-d'),
    ]));

    $response->assertStatus(302);

    $rent = Rent::first();
    $this->assertCount(8, $rent->rentInvoices);
  }

  public function testItShouldCreateRentWithPaidInvoicesGenerated() {
    $this->seed();
    $this->actingAs($this->user);

    $response = $this->post('/rents', array_merge($this->rentData, [
      'deposit' => 12000,
      'amount' => 6000,
      'date' => now()->subRealMonths(8)->format('Y-m-d'),
      'deposit_due' => now()->subRealMonths(8)->format('Y-m-d'),
      'first_invoice_date' => now()->subRealMonths(7)->format('Y-m-d'),
    ]));

    $response->assertStatus(302);

    $rent = Rent::first();
    $this->assertGreaterThanOrEqual(7, $rent->rentInvoices()->count());
  }

  public function testItShouldUpdateRent() {
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $response = $this->put("/rents/{$rent->id}", [
      "notes" => 'Hello i am a note'
    ]);
    $response->assertStatus(302);
    $rent = Rent::find($rent->id);
    $this->assertEquals($rent->notes, 'Hello i am a note');
  }

  public function testItShouldNotUpdateNotAllowedValues() {
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $response = $this->put("/rents/{$rent->id}", [
      "id" => $rent->id + 1,
      "property_id" => $rent->property_id + 1,
      "client_id" => $rent->client->id + 1
    ]);
    $response->assertStatus(302);
    $rentUpdated = Rent::find($rent->id);
    $this->assertEquals($rentUpdated->property_id, $rent->property->id);
  }

  public function testItShouldDeleteRent() {
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $response = $this->delete("/rents/{$rent->id}");
    $response->assertStatus(302);
    $this->assertEquals(Rent::STATUS_CANCELLED, $rent->fresh()->status);
  }

  public function testItShouldCreateRentWithEndDate() {
    $this->seed();
    $this->actingAs($this->user);

    $response = $this->post('/rents', array_merge($this->rentData, [
      'deposit' => 12000,
      'amount' => 6000,
      'date' => '2022-01-15',
      'deposit_due' => '2022-01-15',
      'first_invoice_date' => '2022-01-30',
      'end_date' => '2022-02-15'
    ]));
    $response->assertStatus(302);
    $rent = Rent::first();
    $this->assertNotEmpty($rent->end_date);
  }

  public function testItGeneratesInvoicesToDate() {
    $this->seed();
    $this->actingAs($this->user);

    $this->post('/rents', array_merge($this->rentData, [
      'deposit' => 12000,
      'amount' => 6000,
      'date' => '2022-01-15',
      'deposit_due' => '2022-01-15',
      'first_invoice_date' => '2022-01-30',
      'end_date' => '2022-12-15'
    ]));

    $rent = Rent::first();
    RentTransactionService::generateUpToDate($rent);
    $this->assertCount(13, $rent->invoices()->get());
  }

  public function testRentClientShouldHaveStatusActive() {
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $this->assertEquals($rent->client->status, ClientStatus::Active);
  }

  public function testRentOwnerShouldHaveStatusActive() {
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $this->assertEquals($rent->owner->status, ClientStatus::Active);
  }
}

