<?php

namespace Tests\Feature\CRM;

use App\Domains\CRM\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientsTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    private User $user;
    private mixed $clientData;

    protected function setup(): void {
      parent::setup();
      $this->user = User::factory()->withPersonalTeam()->create();
      $this->clientData = [
        'names' => 'John',
        'address_details' => 'Your house is my house',
        'lastnames' => 'Doe',
        'dni' => '1234567',
        'dni_type' => 'cedula',
        'cellphone' => $this->faker->phoneNumber(),
        'email' => $this->faker->email(),
        'work_name' => 'Insane Code',
      ];
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testItShouldGetClients()
    {
        $this->actingAs($this->user);

        $response = $this->get('/clients');

        $response->assertStatus(200);
    }

    public function testItShouldCreateClient() {
      $this->actingAs($this->user);


      $response = $this->post('/clients', $this->clientData);
      $response->assertStatus(302);
      $this->assertCount(1, Client::all());
    }

    public function testItShouldNotCreateDuplicatedClient() {
      $this->actingAs($this->user);

      $response = $this->post('/clients', $this->clientData);
      $response->assertStatus(302);
      $this->assertCount(1, Client::all());

      $response = $this->post('/clients', $this->clientData);
      $this->assertCount(1, Client::all());

      $response = $this->post('/clients', array_merge($this->clientData, [
        'dni' => '1-23456-7',
      ]));
      $this->assertCount(1, Client::all());
    }

    public function testItShouldUpdateClient() {
      $this->actingAs($this->user);
      $client = Client::factory()->create($this->clientData);

      $response = $this->put("/clients/{$client->id}", array_merge($this->clientData, [
        "names" => 'Lebron James'
      ]));
      $response->assertStatus(302);
      $client = Client::find($client->id);
      $this->assertEquals($client->names, 'Lebron James');
    }

    public function testItShouldDeleteClient() {
      $this->actingAs($this->user);
      $client = Client::factory()->create($this->clientData);

      $response = $this->delete("/clients/{$client->id}");
      $response->assertStatus(302);
      $this->assertCount(0, Client::all());
    }
}
