<?php

use App\Http\Controllers\Api\AccountApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ClientApiController;
use App\Http\Controllers\Api\PropertyApiController;
use App\Http\Controllers\Api\RentApiController;
use App\Http\Controllers\Api\TransactionLineApiController;
use App\Http\Controllers\BackgroundController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;

use App\Http\Controllers\CRM\ClientController;
use App\Http\Controllers\Properties\PropertyController;
use App\Http\Controllers\Properties\PropertyOwnerController;
use App\Http\Controllers\Properties\PropertyTransactionController;
use App\Http\Controllers\Properties\PropertyUnitController;
use App\Http\Controllers\Properties\RentController;
use App\Http\Controllers\Properties\TenantRentController;
use App\Http\Controllers\ReportController;
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

Route::middleware(['auth:sanctum', 'verified'])->prefix('/api')->name('api.')->group(function () {
  //  accounts and transactions

    Route::apiResource('/settings', SettingsController::class, [
     "only" => ['index', 'store', 'update', 'delete']
    ])->names([
     'index' => 'api.settings.index',
     'store' => 'api.settings.store',
     'update' => 'api.settings.update',
     'delete' => 'api.settings.delete',
    ]);

    Route::apiResource('/accounts', AccountApiController::class);
    Route::apiResource('/categories', CategoryApiController::class);
    Route::apiResource('transaction-lines', TransactionLineApiController::class);

    Route::apiResource('clients', ClientApiController::class);

    Route::apiResource('properties', PropertyApiController::class);
    Route::apiResource('rents', RentApiController::class);
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
    Route::get('/dashboard/{section?}', DashboardController::class)->name('dashboard');

     // settings
     Route::resource('/settings', SettingsController::class);
     Route::get('/settings/{name}', function() {
      return "Hello world";
     });

    Route::get('/search', [SearchController::class, 'index']);
     // CRM
    Route::resource('/clients', ClientController::class);
    Route::get('/contacts/{type}', [ClientController::class, 'byTypes']);
    Route::get('/contacts/{client}/{type}', [ClientController::class, 'showByType']);
    Route::get('/clients/{client}/{section}', [ClientController::class, 'getSection']);
    // Business


    // Properties
    Route::get('properties/management-tools', [PropertyController::class, 'managementTools']);
    Route::resource('properties', PropertyController::class);
    Route::post('properties/{property}/units', [PropertyController::class, 'addUnit']);
    Route::get('units', [PropertyUnitController::class, 'index']);

    // rents
    Route::resource('rents', RentController::class);
    // property transactions
    Route::get('/properties/transactions/{category}', PropertyTransactionController::class);
    Route::post('/rents/{rent}/invoices/{invoice}/pay', [RentController::class, 'payInvoice']);
    Route::post('/rents/{rent}/generate-next-invoice', [RentController::class, 'generateNextInvoice']);
    Route::post('/rents/{rent}/transactions/{invoice}', [ClientController::class, 'generateOwnerDistribution']);
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
    Route::get('/clients/{client}/rents/{rent}/end', [TenantRentController::class, 'endRent'])->name('tenant.end-rent');;
    Route::put('/clients/{client}/rents/{rent}/end', [TenantRentController::class, 'endRentAction'])->name('tenant.end-rent-action');
    Route::get('/contacts/{client}/tenants/rents/{rent}/renew', [TenantRentController::class, 'renewRent'])->name('tenant.renew-rent');;
    Route::put('/contacts/{client}/tenants/rents/{rent}/renew', [TenantRentController::class, 'renewRentAction'])->name('tenant.renew-rent-action');

    // Reports
    Route::get('/statements/{category}', [ReportController::class, 'statements'])->name('statements.category');
    Route::get('/reports/{category}', [ReportController::class, 'category'])->name('report.category');
  });

// Loans
Route::group([],  app_path('/Domains/Loans/routes.php'));
Route::group([],  app_path('/Domains/Admin/routes.php'));
