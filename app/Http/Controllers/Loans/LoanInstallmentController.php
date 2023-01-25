<?php

namespace App\Http\Controllers\Loans;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use App\Domains\Loans\Services\LoanTransactionsService;
use App\Http\Controllers\InertiaController;
use Illuminate\Http\Request;

class LoanInstallmentController extends InertiaController
{
    public function updateStatus(Loan $loan, LoanInstallment $repayment) {
      if (request()->user()->current_team_id == $loan->team_id) {
        $repayment->updateStatus();
      }
    }

    public function pay(Loan $loan, LoanInstallment $installment, Request $request) {
      $postData = $this->getPostData();
      LoanTransactionsService::payRepayment($loan, $installment, $postData);
  }
}
