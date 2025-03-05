<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Property\Helpers\PropertyBase;

class RentOptionsTest extends PropertyBase
{
  public function testItShouldExtendRent() {
    $this->actingAs($this->user);
    $rent = $this->createRent([
      'end_date' => Carbon::now()->addRealMonths(12)
    ]);
    $newDate = InvoiceHelper::getNextScheduleDate($rent->end_date, 'MONTHLY');

    $response = $this->put("/contacts/{$rent->client_id}/tenants/rents/{$rent->id}/renew", [
      "end_date" => $newDate,
      'amount' => 10000
    ]);
    $response->assertStatus(302);
    $rent = Rent::find($rent->id);
    $this->assertEquals($rent->end_date, $newDate);
    $this->assertEquals($rent->amount, 10000);
    $this->assertEquals($rent->status, Rent::STATUS_ACTIVE);
    $this->assertEquals($rent->unit->status, PropertyUnit::STATUS_RENTED);
  }

  public function testItShouldEndRent() {
    $this->actingAs($this->user);
    $rent = $this->createRent([
      'end_date' => Carbon::now()->addRealMonths(12)
    ]);
    $newDate = InvoiceHelper::getNextScheduleDate($rent->end_date, 'MONTHLY');

    $response = $this->put("/contacts/{$rent->client_id}/tenants/rents/{$rent->id}/end", [
      "move_out_at" => $newDate,
      'move_out_notice' => "This tenant ended his thing"
    ]);
    $response->assertStatus(302);
    $rent = Rent::find($rent->id);
    $this->assertEquals($rent->end_date, $newDate);
    $this->assertEquals($rent->status, Rent::STATUS_CANCELLED);
    $this->assertEquals($rent->unit->status, PropertyUnit::STATUS_AVAILABLE);
  }

  public function testItShouldExtendAnExpiredRent() {
    $this->actingAs($this->user);
    $rent = $this->createRent([
      'end_date' => Carbon::now()->subRealMonths(2),
    ]);

    $newDate = InvoiceHelper::getNextScheduleDate($rent->end_date, 'MONTHLY');

    
    Artisan::call('background:check-expired-rents');
    $oldUnitStatus = $rent->unit->refresh()->status;
    

    $response = $this->put("/contacts/{$rent->client_id}/tenants/rents/{$rent->id}/renew", [
      "end_date" => $newDate,
      'amount' => 10000
    ]);
    $response->assertStatus(302);
    $this->assertEquals(PropertyUnit::STATUS_AVAILABLE, $oldUnitStatus);
    $rent = Rent::find($rent->id);
    $this->assertEquals($rent->end_date, $newDate);
    $this->assertEquals($rent->amount, 10000);
    $this->assertEquals($rent->status, Rent::STATUS_ACTIVE);
    $this->assertEquals($rent->unit->status, PropertyUnit::STATUS_RENTED);
  }
}

