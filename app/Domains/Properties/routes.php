<?php

use App\Domains\Properties\Http\Controllers\Api\InvoiceApiController;
use App\Domains\Properties\Http\Controllers\Api\PaymentApiController;
use App\Domains\Properties\Http\Controllers\PropertyController;
use App\Domains\Properties\Http\Controllers\PropertyInvoiceController;
use App\Domains\Properties\Http\Controllers\PropertyOwnerController;
use App\Domains\Properties\Http\Controllers\PropertyTransactionController;
use App\Domains\Properties\Http\Controllers\PropertyUnitController;
use App\Domains\Properties\Http\Controllers\RentController;
use App\Domains\Properties\Http\Controllers\RentReportController;
use App\Domains\Properties\Http\Controllers\TenantRentController;
use Illuminate\Support\Facades\Route;


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
    // Properties

    Route::resource('properties', PropertyController::class);

    // Units
    Route::controller(PropertyUnitController::class)->group(function() {
      Route::get('units/bad-unit-status', 'listBadState');
      Route::get('units', 'index');
      Route::post('properties/{property}/units', 'addUnit');
      Route::put('properties/{property}/units/{propertyUnit}', 'updateUnit');
      Route::delete('properties/{property}/units/{propertyUnit}','removeUnit');
    });

    // rents
    Route::resource('rents', RentController::class);
    Route::get('rents/advanced-filter/pending-generation', [RentController::class, 'withPendingGeneration']);
    Route::get('/rents/{rent}/{section}', [RentController::class, 'getSection']);
    
    Route::put('/rents/{rent}/invoices/{invoice}/simple-update', [PropertyInvoiceController::class, 'simpleUpdate']);
    Route::post('/rents/{rent}/invoices/{invoice}/payments', [RentController::class, 'payInvoice']);
    Route::delete('/rents/{rent}/invoices/{invoice}/payments', [RentController::class, 'deleteInvoicePayments']);
    Route::delete('/rents/{rent}/invoices/{invoice}/payments/{payment}', [RentController::class, 'deletePayment']);
    Route::put('/rents/{rent}/invoices/{invoice}/payments/{payment}', [RentController::class, 'updatePayment']);
    Route::get('/invoices/{invoice}/payments/{payment}/print', [PropertyInvoiceController::class, 'printPayment']);
    Route::post('/rents/{rent}/generate-next-invoice', [RentController::class, 'generateNextInvoice']);


    // reports
    Route::get('/rent-reports/monthly-summary', [RentReportController::class, 'monthlySummary']);
    Route::get('/property-reports', [RentReportController::class, 'management']);

    // property transactions
    Route::controller(PropertyTransactionController::class)->group(function () {
      Route::get('/properties/transactions/{category}', '__invoke');
      Route::get('/rents/{rent}/transactions/deposit-refund/create',  'createDepositRefund')->name('property.transactions.create-refund');
      Route::post('/properties/{rent}/transactions/{type}',  'store');
      Route::post('/properties/{rent}/transactions/{type}/{invoiceId}', 'store');
    });

   ;

    // Owner
    Route::controller(PropertyOwnerController::class)->group(function() {
      Route::post('/clients/{client}/owner-distributions', 'generateDraw')->name('owners.draw.generate');
      Route::put('/clients/{client}/owner-distributions/{invoice}', 'generateDraw')->name('owners.draw.update-generation');
      Route::get('/owners/draws', '__invoke')->name('owners.draw');
      Route::post('/owners/{client}/draws', 'storeDraws')->name('owners.draw.store');
      Route::post('/owners/{client}/draws/{drawId}', 'updateDraws')->name('owners.draw.update');
      Route::post('/owners/{client}/draws/{drawId}/payments', 'payDraw')->name('owners.draw.pay');
    });

    // Tenant
    Route::controller(TenantRentController::class)->group(function() {
      Route::get('/clients/{client}/rents/{rent}/end', 'endRent')->name('tenant.end-rent');;
      Route::put('/clients/{client}/rents/{rent}/end', 'endRentAction')->name('tenant.end-rent-action');
      Route::get('/contacts/{client}/tenants/rents/{rent}/renew', 'renewRent')->name('tenant.renew-rent');;
      Route::put('/contacts/{client}/tenants/rents/{rent}/renew', 'renewRentAction')->name('tenant.renew-rent-action');
    });
  });
