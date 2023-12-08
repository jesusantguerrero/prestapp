<?php

namespace Tests\Feature\Property;

use App\Domains\Properties\Models\Rent;
use App\Notifications\InvoiceGenerated;
use Insane\Journal\Models\Invoice\Invoice;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Property\Helpers\PropertyBase;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Services\PropertyTransactionService;

class OwnerDistributionTest extends PropertyBase
{

    public function generateInvoices() {
      $this->actingAs($this->user);

      $rent = $this->createRent([
        "date" => '2022-12-01',
        "price" => 5000,
        "deposit" => 5000,
        "interest_rate" => 10
      ]);

      $this->createExpense($rent->property, [
        "date" => '2023-01-15',
        "amount" => 500
      ]);

      return $rent;
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
      $invoice = $rent->property->expenses()->where('category_type', PropertyInvoiceTypes::UtilityExpense->value)->first();
      $this->payInvoice($rent, $invoice);


      $this->assertEquals(Invoice::STATUS_PAID, $invoice->fresh()->status);
      $this->assertEquals(PropertyInvoiceTypes::UtilityExpense->value, $invoice->category_type);
    }


    public function testItShouldCreateOwnerDistribution()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);

      PropertyTransactionService::createOwnerDistribution($this->owner->fresh());
      $this->assertEquals('owner_distribution', $rent->owner->invoices()->first()->category_type);
    }

    public function testItShouldMatchAmounts()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);

      PropertyTransactionService::createOwnerDistribution($this->owner);

      $this->assertEquals(9000, $this->owner->fresh()->invoices()->first()->total);
    }

    public function testItShouldSendNotification()
    {
      Notification::fake();
      $rent = $this->generateInvoices();

      $this->payInvoices($rent);
      PropertyTransactionService::createOwnerDistribution($this->owner);

      Notification::assertSentTo(
          [$rent->user], InvoiceGenerated::class
      );
    }

    public function testItShouldCreateOwnerDistributionManually()
    {
      $rent = $this->generateInvoices();
      $this->payInvoices($rent);


      return $this->post("/owners/{$this->owner->id}/draws", [
          'invoices' => $this->owner->fresh()->getPropertyInvoices()
        ]
      );

      $this->assertEquals('owner_distribution', $rent->owner->fresh()->invoices()->first()->category_type);
    }
}
