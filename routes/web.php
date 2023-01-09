<?php

use App\Http\Controllers\BackgroundController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;

use App\Http\Controllers\CRM\ClientController;
use App\Http\Controllers\Loans\LoanController;
use App\Http\Controllers\Properties\PropertyController;
use App\Http\Controllers\Properties\RentController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/background/run', BackgroundController::class);
Route::get('/background/update-late-payments', [BackgroundController::class, 'updateLatePayments']);
Route::get('/background/generate-rent-invoices', [BackgroundController::class, 'generateRentInvoices']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

     // settings
     Route::resource('/settings', SettingsController::class);
     Route::get('/settings/tab/{tabName}', [SettingsController::class, 'index']);
     Route::get('/settings/{name}', [SettingsController::class, 'section']);

     Route::apiResource('/api/settings', SettingsController::class);
     Route::apiResource('/api/taxes', TaxController::class);

    // CRM
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{client}/{section}', [ClientController::class, 'getSection']);

    // Business
    // Tenant
    Route::get('/clients/{client}/rents/{rent}/end', [ClientController::class, 'endRent'])->name('tenant.end-rent');;
    Route::put('/clients/{client}/rents/{rent}/end', [ClientController::class, 'endRentAction'])->name('tenant.end-rent-action');
    // Owner
    Route::post('/clients/{client}/owner-distributions', [ClientController::class, 'generateOwnerDistribution']);
    Route::put('/clients/{client}/owner-distributions/{invoice}', [ClientController::class, 'generateOwnerDistribution']);

    // Loans
    Route::get('loans/overview', LoanController::class);
    Route::resource('loans', LoanController::class);
    Route::post('/loans/:loanId/installments/:installment/mark-as-paid', [LoanController::class, 'markAsPaid']);
    Route::post('/loans/{loan}/installments/{installment}/pay', [LoanController::class, 'payInstallment']);
    Route::post('/loans/{loan}/pay', [LoanController::class, 'pay']);
    Route::get('/loans/{loan}/payments/{paymentDocument}/print', [LoanController::class, 'printPaymentDocument']);

    Route::resource('/loan-products', LoanProductController::class);

    // Properties
    Route::get('properties/overview', PropertyController::class);
    Route::get('properties/management-tools', [PropertyController::class, 'managementTools']);
    Route::resource('properties', PropertyController::class);
    Route::resource('rents', RentController::class);
    Route::post('rents/{rent}/invoices/{invoice}/pay', [RentController::class, 'payInvoice']);
    Route::post('rents/{rent}/generate-next-invoice', [RentController::class, 'generateNextInvoice']);
});
