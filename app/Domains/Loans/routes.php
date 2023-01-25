<?php

use App\Http\Controllers\Loans\LoanAgreementController;
use App\Http\Controllers\Loans\LoanController;
use App\Http\Controllers\Loans\LoanInstallmentController;
use Illuminate\Support\Facades\Route;

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

      Route::post('/loans/{loan}/pay', 'pay');
      Route::post('/loans/{loan}/payoff', 'payoff');
      Route::post('/loans/{loan}/update-status', 'updateStatus');
      Route::get('/loans/{loan}/payments/{paymentDocument}/print', 'printPaymentDocument');
      Route::post('/loans/:loanId/installments/:installment/mark-as-paid', 'markAsPaid');
    });

    Route::controller(LoanInstallmentController::class)->group(function () {
        Route::post('/loans/{loan}/installments/{installment}/pay', 'pay');
        Route::post('/loans/{loan}/installments/{installment}/update-status', 'updateStatus');
    });

    Route::controller(LoanAgreementController::class)->group(function () {
        Route::post('/loans/{loan}/agreements', 'store');
    });

    Route::get('/payment-center', [LoanController::class, 'paymentCenter']);
    Route::resource('/loan-products', LoanProductController::class);

  });
