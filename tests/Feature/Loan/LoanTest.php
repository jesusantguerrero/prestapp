<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanTest extends TestCase
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

    $date = date('Y-m-d');
    $firstRepaymentDate = InvoiceHelper::getNextDate($date);

    $this->loanData = [
      'client_id' => $this->lender->id,
      'amount' => 1000,
      'interest_rate' => 10,
      'repayment_count' => 4,
      'date' => $date,
      'disbursement_date' => $date,
      'fist_installment_date' => $firstRepaymentDate,
      'frequency' => 'MONTHLY'
    ];
  }

  private function createProperty() {
    $this->actingAs($this->user);
    $this->post('/properties', $this->propertyData);

    return Property::latest()->first();
  }

  /**
   * A basic feature test example.
   *
   * @return void
   */
  public function testItShouldGetProperties()
  {
      $this->actingAs($this->user);

      $response = $this->get('/properties');

      $response->assertStatus(200);
  }

  public function testItShouldCreateProperty() {
    $this->actingAs($this->user);

    $response = $this->post('/properties', $this->propertyData);
    $response->assertStatus(302);
    $this->assertCount(1, Property::all());
  }

  public function testItShouldCreatePropertyWithMultipleUnits() {
    $this->actingAs($this->user);

    $units = PropertyUnit::factory()->count(3)
    ->priced([6000, 7000, 6000])->make()->toArray();

    $response = $this->post('/properties', array_merge(
      $this->propertyData,
      [
        "units" => $units,
      ]
    ));
    $response->assertStatus(302);

    $this->assertCount(1, Property::all());
    $this->assertCount(3, PropertyUnit::all());
  }

  public function testItShouldUpdateProperty() {
    $this->actingAs($this->user);
    $property = $this->createProperty();

    $response = $this->put("/properties/{$property->id}", [
      "name" => 'My first property'
    ]);
    $response->assertStatus(302);
    $property = Property::find($property->id);
    $this->assertEquals($property->name, 'My first property');
  }

  public function testItShouldDeleteProperty() {
    $this->actingAs($this->user);
    $property = $this->createProperty();

    $response = $this->delete("/properties/{$property->id}");
    $response->assertStatus(302);
    $this->assertCount(0, Property::all());
  }
}
