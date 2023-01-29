<?php

namespace Tests\Feature\Owners;

use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyTransactionService;

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
      foreach ($rent->fresh()->invoices as $invoice) {
        $this->payInvoice($rent, $invoice);
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

    public function testItShouldPayInvoiceDebt()
    {
      $rent = $this->generateInvoices();
      $invoice = $rent->invoices()->first();
      $this->payInvoice($rent, $invoice);

      $this->assertEquals(Invoice::STATUS_PAID, $invoice->fresh()->status);
    }

    public function testItShouldPayExpenseDebt()
    {
      $rent = $this->generateInvoices();
      $invoice = $rent->invoices()->where('category_type', PropertyInvoiceTypes::UtilityExpense->value)->first();
      $this->payInvoice($rent, $invoice);

      $this->assertEquals(Invoice::STATUS_PAID, $invoice->fresh()->status);
      $this->assertEquals(PropertyInvoiceTypes::UtilityExpense->value, $invoice->category_type);
    }



    public function testOwnerShouldHavePropertyInvoices()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);
      $this->assertCount(3, $rent->owner->getPropertyInvoices());
    }

    public function testItShouldCreateOwnerDistribution()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);

      PropertyTransactionService::createOwnerDistribution($this->owner->fresh());
      $this->assertEquals('owner_distribution', $rent->owner->invoices()->first()->category_type);
    }

    // public function testItShouldMatchAmounts()
    // {
    //   $rent = $this->generateInvoices();
    //   $this->payInvoices($rent);

    //   PropertyTransactionService::createOwnerDistribution($this->owner);

    //   $this->assertEquals(8700, $this->owner->fresh()->invoices()->first()->total);
    // }
}
