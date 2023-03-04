<?php

use App\Domains\Properties\Http\Controllers\PropertyController;
use App\Domains\Properties\Http\Controllers\PropertyOwnerController;
use App\Domains\Properties\Http\Controllers\PropertyTransactionController;
use App\Domains\Properties\Http\Controllers\PropertyUnitController;
use App\Domains\Properties\Http\Controllers\RentController;
use App\Domains\Properties\Http\Controllers\TenantRentController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'verified'])->prefix('/api')->name('api.')->group(function () {

});

Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {
    // Properties
    Route::get('properties/management-tools', [PropertyController::class, 'managementTools']);
    Route::resource('properties', PropertyController::class);
    Route::post('properties/{property}/units', [PropertyController::class, 'addUnit']);
    Route::get('units', [PropertyUnitController::class, 'index']);

    // rents
    Route::resource('rents', RentController::class);

    // property transactions
    Route::post('/rents/{rent}/invoices/{invoice}/pay', [RentController::class, 'payInvoice']);
    Route::post('/rents/{rent}/generate-next-invoice', [RentController::class, 'generateNextInvoice']);
    Route::get('/properties/transactions/{category}', PropertyTransactionController::class);
    Route::post('/properties/{rent}/transactions/{type}', [PropertyTransactionController::class, 'store']);
    Route::post('/properties/{rent}/transactions/{type}/{invoiceId}', [PropertyTransactionController::class, 'store']);

    // Owner
    Route::controller(PropertyOwnerController::class)->group(function() {
      Route::post('/clients/{client}/owner-distributions', 'generateDraw');
      Route::put('/clients/{client}/owner-distributions/{invoice}', 'generateDraw');
      Route::get('/owners/draws', '__invoke')->name('owners.draw');
      Route::post('/owners/{client}/draws', 'storeDraws')->name('owners.draw.store');
      Route::post('/owners/{client}/draws/{drawId}', 'updateDraws')->name('owners.draw.update');
    });

    // Tenant
    Route::controller(TenantRentController::class)->group(function() {
      Route::get('/clients/{client}/rents/{rent}/end', 'endRent')->name('tenant.end-rent');;
      Route::put('/clients/{client}/rents/{rent}/end', 'endRentAction')->name('tenant.end-rent-action');
      Route::get('/contacts/{client}/tenants/rents/{rent}/renew', 'renewRent')->name('tenant.renew-rent');;
      Route::put('/contacts/{client}/tenants/rents/{rent}/renew', 'renewRentAction')->name('tenant.renew-rent-action');
    });
  });
