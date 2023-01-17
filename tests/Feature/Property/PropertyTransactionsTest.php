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
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;
use Tests\TestCase;

class PropertyTransactionsTest extends TestCase
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

  public function testItShouldCreateAPropertyExpense() {
    $this->seed();
    $this->actingAs($this->user);
    $rent = $this->createRent();

    $response = $this->post("/properties/{$rent->id}/transactions/expense", array_merge($this->rentData, [
      'client_id' => $rent->client_id,
      'account_id' => Account::guessAccount($rent, ['Property Expenses', 'expenses']),
      'amount' => 1000,
      'date' => '2023-01-30',
      'details' => 'Fix front door',
      'concept' => 'fix front door',
    ]));
    $response->assertStatus(200);
    $rent = Rent::first();

    $this->assertCount(1, $rent->rentExpenses);
    $this->assertEquals(1000, $rent->rentExpenses[0]->total);
    $this->assertEquals(Invoice::STATUS_UNPAID, $rent->rentExpenses[0]->status);
  }
}