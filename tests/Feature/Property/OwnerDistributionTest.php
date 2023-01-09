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

    $this->property = Property::factory()->create([
      'user_id' => $this->user->id,
      'team_id' => $this->user->current_team_id,
      'owner_id' => $this->owner->id
    ]);
  }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testItShouldCreateOwnerDistribution()
    {
      $this->actingAs($this->user);

      //  RentService::createRent([
      //   'property_id' => $this->property->id,
      //   'client_id' => $this->client->id,
      //   'user_id' => $this->user->id,
      //   'team_id' => $this->user->current_team_id,
      //   'deposit' => 2500,
      //   ]
      // );

      // GenerateInvoices::ownerDistribution($this->owner);

      // $this->assertCount(1, Invoice::all());
    }
}
