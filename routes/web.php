<?php

use App\Http\Controllers\BackgroundController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RentController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Insane\Journal\Helpers\ReportHelper;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // CRM
    Route::resource('clients', ClientController::class);
    
    // Loans
    Route::resource('loans', LoanController::class);
    Route::post('/loans/:loanId/installments/:installment/mark-as-paid', [LoanController::class, 'markAsPaid']);
    Route::post('/loans/{loan}/installments/{installment}/pay', [LoanController::class, 'payInstallment']);
    Route::post('/loans/{loan}/pay', [LoanController::class, 'pay']);

    // Properties
    Route::resource('rents', RentController::class);
});
