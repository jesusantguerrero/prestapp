<?php

namespace Tests\Feature\Owners;

use App\Domains\CRM\Models\Client;
use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Actions\GenerateInvoices;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Services\RentService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Insane\Journal\Models\Invoice\Invoice;
use Tests\TestCase;

class OwnerDistributionTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testItShouldCreateOwnerDistribution()
    {
      $user = User::factory()->create();
      $this->actingAs($user);

      $owner = Client::factory()->create([
        'user_id' => $user->id,
        'team_id' => $user->current_team_id,
      ]);

      $client = Client::factory()->create([
        'user_id' => $user->id,
        'team_id' => $user->current_team_id,
      ]);

      $property = Property::factory()->create([
        'user_id' => $user->id,
        'team_id' => $user->current_team_id,
        'owner_id' => $owner->id
      ]);

      $rent = RentService::createRent([
        'property_id' => $property->id,
        'client_id' => $client->id,
        'user_id' => $user->id,
        'team_id' => $user->current_team_id,
        'deposit' => 2500,
        ]
      );

      GenerateInvoices::ownerDistribution($owner);      

      $this->assertCount(1, Invoice::all());
    }
}
