<?php

namespace Tests\Feature\Property;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\Rent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
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
  }
}
