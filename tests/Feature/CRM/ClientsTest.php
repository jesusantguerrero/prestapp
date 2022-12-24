<?php

namespace Tests\Feature\CRM;

use App\Domains\CRM\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testItShouldGetClients()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $client = Client::factory()->create([
          'user_id' => $user->id,
          'team_id' => $user->current_team_id,
        ]);
        
        $response = $this->get('/clients');

        $response->assertStatus(200);
    }
}
