<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Academic\Http\Controllers\ClassroomController;
use App\Domains\Properties\Http\Controllers\Api\InvoiceApiController;
use App\Domains\Properties\Http\Controllers\Api\PaymentApiController;



Route::middleware(['auth:sanctum', 'verified'])->prefix('/api')->name('api.')->group(function () {
  Route::apiResource('invoices', InvoiceApiController::class);
  Route::apiResource('rent-payments', PaymentApiController::class);
});

Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'atmosphere.teams-approved',
  'verified'
])->group(function () {
    // Classrooms
    Route::resource('classrooms', ClassroomController::class);
  });
