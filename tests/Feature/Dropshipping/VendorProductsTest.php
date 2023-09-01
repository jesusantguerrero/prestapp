<?php

namespace Tests\Feature\CRM;

use App\Domains\Dropshipping\Services\VendorProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VendorProductsTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testItShouldGetVendorProducts()
    {

        $url = "https://us.shein.com/Manfinity-Sporsity-Men-Cut-And-Sew-Polo-Shirt-p-753301-cat-1981.html?mallCode=1";
        $response = (new VendorProductService())->getFromSheinUrl($url);
        dd($response->body());
        $response->assertStatus(200);
    }
}
