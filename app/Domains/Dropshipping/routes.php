<?php

use App\Domains\Dropshipping\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'atmosphere.teams-approved',
  'verified'
])->group(function () {
    // orders
    Route::resource('orders', OrderController::class);
    Route::get('/orders/{order}/actions/{action}', [OrderController::class, 'action']);
});
