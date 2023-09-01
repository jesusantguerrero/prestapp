<?php

namespace App\Domains\Dropshipping\Services;

use Illuminate\Support\Facades\Http;

class VendorProductService
{
    public function getFromSheinUrl(string $productUrl) {
      $endpoint = config("atmosphere.dropshipping.serviceUrl");
      return Http::get("$endpoint?vendor=shein&search=$productUrl");
    }
}
