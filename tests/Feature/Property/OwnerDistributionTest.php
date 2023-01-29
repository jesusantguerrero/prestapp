<?php

namespace Tests\Feature\Owners;

use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyTransactionService;

use Insane\Journal\Models\Core\Account;
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
      foreach ($rent->fresh()->invoices as $invoice) {
        $url = $invoice->category_type == PropertyInvoiceTypes::Rent
        ? "/rents/$rent->id/invoices/$invoice->id/pay"
        : "/invoices/$invoice->id/pay";

        $this->post($url, [
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
      $this->assertEquals(10666.67, $rent->fresh()->invoices()->sum('total'));
    }

    public function testItShouldCreateOwnerDistribution()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);
      PropertyTransactionService::createOwnerDistribution($this->owner);

      $this->assertEquals(1, $rent->owner->fresh()->invoices()->count());
      $this->assertEquals('owner_distribution', $rent->owner->fresh()->invoices()->first()->category_type);
    }

    public function testItShouldMatchAmounts()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);

      PropertyTransactionService::createOwnerDistribution($this->owner);

      $this->assertEquals(8700, $this->owner->fresh()->invoices()->first()->total);
    }
}
