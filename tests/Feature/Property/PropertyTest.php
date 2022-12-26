<?php

namespace Tests\Feature\Property;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropertyTest extends TestCase
{
  use WithFaker;
  use RefreshDatabase;

  protected function setup(): void {
    parent::setup();
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

    $this->propertyData = [
      'address' => $this->faker->address(),
      'price' => 2000
    ];
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

  // public function testItShouldUpdateClient() {
  //   $this->actingAs($this->user);
  //   $client = Client::factory()->create($this->clientData);

  //   $response = $this->put("/clients/{$client->id}", array_merge($this->clientData, [
  //     "names" => 'Lebron James'
  //   ]));
  //   $response->assertStatus(302);
  //   $client = Client::find($client->id);
  //   $this->assertEquals($client->names, 'Lebron James');
  // }

  // public function testItShouldDeleteClient() {
  //   $this->actingAs($this->user);
  //   $client = Client::factory()->create($this->clientData);

  //   $response = $this->delete("/clients/{$client->id}");
  //   $response->assertStatus(302);
  //   $this->assertCount(0, Client::all());
  // }
}
