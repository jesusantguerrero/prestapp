<?php

namespace Tests\Feature\Property\Helpers;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Insane\Journal\Models\Core\Account;
use Tests\TestCase;

class PropertyBase extends TestCase
{
  use WithFaker;
  use RefreshDatabase;

  protected User $user;
  protected Client $client;
  protected Client $owner;
  protected Property $property;
  protected mixed $rentData;

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
      "deposit_reference" => "",
      "payment_account_id" => null,
      "payment_method" => "",
      "amount" => 5000,
      "first_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
      "next_invoice_date" => $firstInvoiceDate->format('Y-m-d'),
      "commission" => 10,
      "commission_type" => "PERCENTAGE",
      "grace_days" => 5,
      "late_fee" => 10,
      "late_fee_type" => "PERCENTAGE",
      "frequency" => "MONTHLY",
      "additional_fees" => [],
    ];
  }

  protected function createRent($data = []) {
    $this->actingAs($this->user);
    $this->post('/rents', array_merge($this->rentData, $data));

    return Rent::latest()->first();
  }

  protected function createExpense($rent, $formData = []) {
    return $this->post("/properties/{$rent->id}/transactions/expense", array_merge($this->rentData, [
      'client_id' => $rent->client_id,
      'account_id' => Account::guessAccount($rent, ['Property Expenses', 'expenses']),
      'amount' => $formData['amount'] ?? 1000,
      'date' => $formData['date'] ?? date('Y-m-d'),
      'details' => 'Fix front door',
      'concept' => 'fix front door',
    ]));
  }

  public function payInvoice(Rent $rent, $invoice, $form = []) {
    $this->actingAs($this->user);

      $url = $invoice->category_type !== PropertyInvoiceTypes::UtilityExpense->value
      ? "/rents/$rent->id/invoices/$invoice->id/payments"
      : "/invoices/$invoice->id/payment";

      return $this->post($url, [
        'client_id' => $rent->client_id,
        'account_id' => $form['account_id'] ?? Account::findByDisplayId('daily_box', $rent->team_id)->id,
        'amount' => $form['amount'] ?? $invoice->debt,
        'date' => date('Y-m-d'),
        'payment_date' => date('Y-m-d'),
        'details' => 'Payment of ' . $invoice->concept,
        'concept' => 'Payment of ' . $invoice->concept,
      ]);
  }

  public function payInvoices(Rent $rent) {
    $this->actingAs($this->user);
    foreach ($rent->invoices()->get() as $invoice) {
      $this->payInvoice($rent, $invoice);
    }
  }

}
