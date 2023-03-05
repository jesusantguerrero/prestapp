<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\RentService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RentTest extends TestCase
{
  use WithFaker;
  use RefreshDatabase;

  private User $user;
  private Client $client;
  private Client $owner;
  private Property $property;
  private mixed $rentData;

  protected function setup(): void {
    parent::setup();
    $this->seed();
    $user = User::factory()->withPersonalTeam()->create();
    $user->current_team_id = $user->fresh()->ownedTeams()->latest('id')->first()->id;
    $user->save();
    $this->user = $user;
    $this->client = Client::factory()->create([
      'user_id' => $this->user->id,
      'team_id' => $this->user->current_team_id,
    ]);
    $this->owner = Client::factory()->create([
      'user_id' => $this->user->id,
      'team_id' => $this->user->current_team_id,
    ]);

    $this->property = Property::factory()
    ->has(PropertyUnit::factory()->count(3), 'units')->create([
      'user_id' => $this->user->id,
      'team_id' => $this->user->current_team_id,
      'owner_id' => $this->owner->id
    ]);

    $date = date('Y-m-d');
    $firstInvoiceDate = InvoiceHelper::getNextDate($date);

    $this->rentData = [
      "property_id" => $this->property->id,
      "unit_id" => $this->property->units[0]->id,
      "client_id" => $this->client->id,
      "date" => $date,
      "deposit" => 5000,
      "deposit_due" => $date,
      "is_deposit_received" => true,
      "deposit_reference" => "",
      "payment_account_id" => null,
      "payment_method" => "",
      "amount" => 5000,
      "first_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
      "next_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
      "commission" => 10,
      "commission_type" => "PERCENTAGE",
      "late_fee" => 10,
      "late_fee_type" => "PERCENTAGE",
      "grace_days" => 5,
      "frequency" => "MONTHLY",
      "additional_fees" => [],
    ];
  }

  private function createRent($data = []) {
    $this->actingAs($this->user);
    $this->post('/rents', array_merge($this->rentData, $data));

    return Rent::latest()->first();
  }

  /**
   * A basic feature test example.
   *
   * @return void
   */
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
    $this->assertCount(1, $rent->rentInvoices);
    $this->assertEquals(3000, $rent->rentInvoices[0]->total);
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
    $this->assertCount(0, Rent::all());
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
    RentService::generateUpToDate($rent);
    $this->assertCount(12, $rent->invoices()->get());
  }

  public function testRentClientShouldHaveStatusActive() {
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $this->assertEquals($rent->client->status, Client::STATUS_ACTIVE);
  }

  public function testRentOwnerShouldHaveStatusActive() {
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $this->assertEquals($rent->owner->status, Client::STATUS_ACTIVE);
  }
}

