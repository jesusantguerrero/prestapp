<?php

namespace Tests\Feature\CRM;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Dropshipping\Services\VendorProductService;

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
        $user = User::factory()->withPersonalTeam()->create();
        $this->actingAs($user);
        $url = "https://us.shein.com/Manfinity-Sporsity-Men-Cut-And-Sew-Polo-Shirt-p-753301-cat-1981.html?mallCode=1";
        $response = $this->get("/dropshipping/vendor-products/shein?search=$url");
        $response->assertStatus(200);
    }
}
