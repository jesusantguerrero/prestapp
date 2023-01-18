<?php

use App\Http\Controllers\Api\ClientApiController;
use App\Http\Controllers\Api\PropertyApiController;
use App\Http\Controllers\Api\RentApiController;
use App\Http\Controllers\Api\TransactionLineApiController;
use App\Http\Controllers\BackgroundController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;

use App\Http\Controllers\CRM\ClientController;
use App\Http\Controllers\Loans\LoanController;
use App\Http\Controllers\Loans\LoanProductController;
use App\Http\Controllers\Properties\PropertyController;
use App\Http\Controllers\Properties\PropertyTransactionController;
use App\Http\Controllers\Properties\PropertyUnitController;
use App\Http\Controllers\Properties\RentController;
use App\Http\Controllers\Properties\TenantRentController;
use App\Http\Controllers\SearchController;
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

Route::middleware(['auth:sanctum', 'verified'])->prefix('/api')->group(function () {
  //  accounts and transactions
Route::resource('clients', ClientApiController::class);
Route::resource('properties', PropertyApiController::class);
Route::resource('rents', RentApiController::class);
Route::resource('transaction-lines', TransactionLineApiController::class);
  // Route::patch('/accounts', [AccountApiController::class,  'bulkUpdate']);
  // Route::resource('categories', CategoryApiController::class);
  // Route::patch('/categories', [CategoryApiController::class,  'bulkUpdate']);
});

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

    Route::get('/search', [SearchController::class, 'index']);
     // CRM
    Route::get('/contacts/{type}', [ClientController::class, 'byTypes']);
    Route::get('/contacts/{client}/{type}', [ClientController::class, 'showByType']);
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{client}/{section}', [ClientController::class, 'getSection']);

    // Business


    // Loans
    Route::get('loans/overview', LoanController::class);
    Route::resource('loans', LoanController::class);
    Route::get('/loans/{loan}/{section}', [LoanController::class, 'getSection']);
    Route::post('/loans/:loanId/installments/:installment/mark-as-paid', [LoanController::class, 'markAsPaid']);
    Route::post('/loans/{loan}/installments/{installment}/pay', [LoanController::class, 'payInstallment']);
    Route::post('/loans/{loan}/pay', [LoanController::class, 'pay']);
    Route::get('/loans/{loan}/payments/{paymentDocument}/print', [LoanController::class, 'printPaymentDocument']);
    Route::get('/payment-center', [LoanController::class, 'paymentCenter']);

    Route::resource('/loan-products', LoanProductController::class);

    // Properties
    Route::get('properties/overview', PropertyController::class);
    Route::get('properties/management-tools', [PropertyController::class, 'managementTools']);
    Route::resource('properties', PropertyController::class);
    Route::post('properties/{property}/units', [PropertyController::class, 'addUnit']);
    Route::get('units', [PropertyUnitController::class, 'index']);

    // rents
    Route::resource('rents', RentController::class);
    // property transactions
    Route::get('/properties/transactions/{category}', PropertyTransactionController::class);
    Route::post('/properties/{rent}/transactions/{type}', [PropertyTransactionController::class, 'store']);
    Route::post('rents/{rent}/invoices/{invoice}/pay', [RentController::class, 'payInvoice']);
    Route::post('rents/{rent}/generate-next-invoice', [RentController::class, 'generateNextInvoice']);
    Route::post('/rents/{rent}/transactions/{invoice}', [ClientController::class, 'generateOwnerDistribution']);

    // Owner
    Route::post('/clients/{client}/owner-distributions', [ClientController::class, 'generateOwnerDistribution']);
    Route::put('/clients/{client}/owner-distributions/{invoice}', [ClientController::class, 'generateOwnerDistribution']);

    // Tenant
    Route::get('/clients/{client}/rents/{rent}/end', [TenantRentController::class, 'endRent'])->name('tenant.end-rent');;
    Route::put('/clients/{client}/rents/{rent}/end', [TenantRentController::class, 'endRentAction'])->name('tenant.end-rent-action');


});

