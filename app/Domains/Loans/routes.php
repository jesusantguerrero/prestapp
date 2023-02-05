<?php

use App\Domains\Loans\Http\Controllers\Api\RepaymentApiController;
use App\Domains\Loans\Http\Controllers\LoanAgreementController;
use App\Domains\Loans\Http\Controllers\LoanController;
use App\Domains\Loans\Http\Controllers\LoanInstallmentController;
use App\Domains\Loans\Http\Controllers\LoanProductController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'verified'])->prefix('/api')->name('api.')->group(function () {
  Route::apiResource('repayments', RepaymentApiController::class);
});

Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {
    Route::get('loans/overview', LoanController::class);
    Route::resource('loans', LoanController::class);

    Route::controller(LoanController::class)->group(function () {
      Route::get('/loan-accounts', 'loanSourceAccounts');
      Route::get('/loan-accounts/{id}', 'loanSourceAccounts');
      Route::get('/loans/{loan}/{section}', 'getSection');

      Route::post('/loans/{loan}/update-status', 'updateStatus');
      Route::post('/loans/{loan}/pay', 'pay');
      Route::post('/loans/{loan}/payoff', 'payoff');
      Route::post('/loans/{loan}/close', 'close');

      // payments
      Route::get('/loans/{loan}/payments/{paymentDocument}/print', 'printPaymentDocument');
      Route::delete('/loans/{loan}/payments/{paymentDocument}', 'deletePaymentDocument');
    });

    // repayments
    Route::controller(LoanInstallmentController::class)->group(function () {
      Route::post('/loans/{loan}/installments/{installment}/pay', 'pay');
      Route::post('/loans/{loan}/installments/{installment}/update-status', 'updateStatus');
      Route::put('/loans/{loan}/installments/{installment}', 'update');
      Route::post('/loans/{loan}/installments/{installment}/mark-as-paid', 'markAsPaid');
    });
  
    Route::get('/payment-center', [LoanController::class, 'paymentCenter']);
    Route::get('/repayments', [LoanInstallmentController::class, 'index']);

    // agreements
    Route::controller(LoanAgreementController::class)->group(function () {
        Route::post('/loans/{loan}/agreements', 'store');
    });

    Route::resource('/loan-products', LoanProductController::class);

  });
