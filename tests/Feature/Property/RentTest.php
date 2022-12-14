<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
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
      "first_invoice_date" => $firstInvoiceDate,
      "next_invoice_date" => $firstInvoiceDate,
      "commission" => 10,
      "commission_type" => "PERCENTAGE",
      "late_fee" => 10,
      "late_fee_type" => "PERCENTAGE",
      "grace_days" => 0,
      "frequency" => "MONTHLY",
      "additional_fees" => [],
    ];
  }

  private function createRent() {
    $this->actingAs($this->user);
    $this->post('/rents', $this->rentData);

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

  // public function testItShouldCreatePropertyWithMultipleUnits() {
  //   $this->actingAs($this->user);

  //   $units = PropertyUnit::factory()->count(3)
  //   ->priced([6000, 7000, 6000])->make()->toArray();

  //   $response = $this->post('/properties', array_merge(
  //     $this->propertyData,
  //     [
  //       "units" => $units,
  //     ]
  //   ));
  //   $response->assertStatus(302);

  //   $this->assertCount(1, Property::all());
  //   $this->assertCount(3, PropertyUnit::all());
  // }

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
}
