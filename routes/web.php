<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BackgroundController;
use App\Http\Controllers\CRM\ClientController;
use App\Http\Controllers\Api\RentApiController;
use App\Http\Controllers\NotificationController;

use App\Http\Controllers\Api\ClientApiController;
use App\Http\Controllers\Api\AccountApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\PropertyApiController;
use App\Domains\Core\Http\Controllers\SSOController;
use App\Http\Controllers\Api\TransactionLineApiController;
use App\Domains\Core\Http\Controllers\AuthClientController;

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

Route::get('/oauth/sso', [SSOController::class, 'connect']);
Route::get('/oauth/accept', [SSOController::class, 'accept']);
Route::get('/oauth/user', [SSOController::class, 'getUser']);

Route::get('/background/run', BackgroundController::class);
Route::get('/background/update-late-payments', [BackgroundController::class, 'updateLatePayments']);
Route::get('/background/generate-rent-invoices', [BackgroundController::class, 'generateRentInvoices']);

Route::impersonate();

Route::middleware(['guest'])->prefix('/portal')->group(function() {
  Route::get('login', [AuthClientController::class, 'show'])->name('login.show');
  Route::post('login', [AuthClientController::class, 'login'])->name('portal.login');
  Route::get('verify/{token}', [AuthClientController::class, 'verify'])->name('verify-login');
});

Route::middleware([
    'auth:web,sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    // settings
    Route::resource('/settings', SettingsController::class);
    Route::get('/settings/{name}', [SettingsController::class, 'index']);
    Route::post('/settings/current-team-photo', [SettingsController::class, 'updateTeamPhoto'])->name('team-profile-photo.update');
    Route::delete('/settings/current-team-photo', [SettingsController::class, 'deleteTeamPhoto'])->name('team-profile-photo.destroy');
    Route::get('/help', function() {
    return inertia('Help');
    });
});

Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
  'atmosphere.teams-approved',
])->group(function () {
    Route::get('/', fn () => redirect("/dashboard"));

    Route::get('/dashboard/{section}', DashboardController::class)->name('dashboard.section');

    Route::get('/search', [SearchController::class, 'index']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications', [NotificationController::class, 'update']);
    Route::put('/notifications/{id}', [NotificationController::class, 'update']);

     // CRM
    Route::resource('/clients', ClientController::class);
    Route::get('/contacts/{type}', [ClientController::class, 'byTypes']);
    Route::get('/contacts/{client}/{type}', [ClientController::class, 'showByType']);
    Route::get('/clients/{client}/{section}', [ClientController::class, 'getSection']);

    // Reports
    Route::get('/statements/{category}', [ReportController::class, 'statements'])->name('statements.category');
    Route::get('/reports/payments', [ReportController::class, 'payments'])->name('report.payments');
    Route::get('/reports/{category}', [ReportController::class, 'category'])->name('report.category');
    // invoicing
    Route::get('/payments', PaymentController::class);
    Route::resource('/invoices', InvoiceController::class);
    Route::post('/invoices/{id}/payment', [InvoiceController::class, 'addPayment']);
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print']);
    Route::post('/invoices/{id}/mark-as-paid', [InvoiceController::class, 'markAsPaid']);
    Route::delete('/invoices/{id}/payment/{paymentId}', [InvoiceController::class, 'deletePayment']);
    Route::get('/invoices/{invoice}/preview', [InvoiceController::class, 'publicPreview']);
    Route::post('/invoices/{invoice}/sign', [InvoiceController::class, 'signInvoice']);
    // Bills
    Route::resource('/bills', InvoiceController::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/data-quality', [App\Http\Controllers\Admin\DataQualityController::class, 'index'])
        ->name('admin.data-quality');
        // ->middleware('can:manage-data-quality');
});

// Admin
Route::group([],  app_path('/Domains/Admin/routes.php'));
// Loans
Route::group([],  app_path('/Domains/Loans/routes.php'));
// properties
Route::group([],  app_path('/Domains/Properties/routes.php'));

Route::group([],  app_path('/Domains/Dropshipping/routes.php'));
