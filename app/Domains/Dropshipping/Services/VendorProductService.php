<?php

namespace App\Domains\Dropshipping\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class VendorProductService
{
    public function getFromSheinUrl(string $productUrl) {
      $endpoint = config("atmosphere.dropshipping.serviceUrl");
      return Http::get("$endpoint?vendor=shein&search=$productUrl");
    }

    public function getProductInfo($source = 'shein') {
      $productUrl = request()->query('search');
      $response = Http::get($productUrl);
      $body = new Crawler($response->body());
      $error = "";

      try {
        $image = $body?->filter('.crop-image-container')?->first()?->attr('data-before-crop-src');
        $name = $body->filter('.product-intro__head-name')->first()->text();
        $id = $body->filter('.product-intro__head-sku')->first()->text();
        $price =  0;
      } catch (Exception $e) {
        $image = "";
        $name = "";
        $id = "";
        $price = "";
        $error = $e->getMessage();
        Log::error($e->getMessage());
      }
      // $price = $body->filter('.product-intro__head-mainprice .original.from')->first()->text() ?? 0;

      return [
          "image" => $image ?? "",
          "productName" => $name ?? "",
          "id" => str_replace('SKU: ', '', $id),
          "price" => $price,
          "error" => $error
      ];
    }
}
