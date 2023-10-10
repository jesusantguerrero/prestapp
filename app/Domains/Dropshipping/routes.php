<?php

use App\Domains\Dropshipping\Http\Controllers\InvoiceController;
use App\Domains\Dropshipping\Http\Controllers\OrderController;
use App\Domains\Dropshipping\Services\VendorProductService;
use Illuminate\Support\Facades\Route;


Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'atmosphere.teams-approved',
  'verified'
])->name('dropshipping.')->prefix('dropshipping')->group(function () {
    // orders
    Route::resource('orders', OrderController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::post('/orders/{order}/actions/{action}', [OrderController::class, 'action']);
    Route::post('/orders/{order}/actions/{action}', [OrderController::class, 'action']);
    Route::get('/vendor-products/{source}', function ($url) {
      return (new VendorProductService())->getProductInfo($url);
    });
});
