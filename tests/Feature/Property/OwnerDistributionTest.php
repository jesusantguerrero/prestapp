<?php

namespace Tests\Feature\Owners;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Actions\GenerateInvoices;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyTransactionService;
use App\Domains\Properties\Services\RentService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;
use Tests\Feature\Property\Helpers\PropertyBase;

class OwnerDistributionTest extends PropertyBase
{

    public function generateInvoices() {
      $this->actingAs($this->user);

      $rent = $this->createRent([
        "price" => 5000,
        "deposit" => 5000,
        "interest_rate" => 10
      ]);

      $this->createExpense($rent, [
        "amount" => 500
      ]);

      return $rent;
    }

    public function payInvoices(Rent $rent) {
      $this->actingAs($this->user);
      foreach ($rent->invoices as $invoice) {
        $this->post("/rents/$rent->id/invoices/$invoice->id/pay", [
          'client_id' => $rent->client_id,
          'account_id' => Account::findByDisplayId('daily_box', $rent->team_id),
          'amount' => $invoice->fresh()->debt,
          'date' => date('Y-m-d'),
          'details' => 'Payment of ' . $invoice->concept,
          'concept' => 'Payment of ' . $invoice->concept,
        ]);
      }
    }

    public function testItShouldGenerateDebt()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);

      PropertyTransactionService::createOwnerDistribution($this->owner);

      $this->assertEquals(3, $rent->fresh()->invoices()->count());
      $this->assertEquals(10500, $rent->fresh()->invoices()->sum('total'));
    }

    public function testItShouldCreateOwnerDistribution()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);

      PropertyTransactionService::createOwnerDistribution($this->owner);

      $this->assertEquals(1, $this->owner->fresh()->invoices()->count());
      $this->assertEquals('owner_distribution', $this->owner->fresh()->invoices->first()->category_type);
    }

    public function testItShouldMatchAmounts()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);

      PropertyTransactionService::createOwnerDistribution($this->owner);

      $this->assertEquals(8700, $this->owner->fresh()->invoices->first()->total);
    }
}
