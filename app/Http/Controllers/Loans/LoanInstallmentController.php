<?php

namespace App\Http\Controllers\Loans;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use App\Domains\Loans\Services\LoanInstallmentService;
use App\Domains\Loans\Services\LoanTransactionsService;
use App\Http\Controllers\Traits\HasEnrichedRequest;
use Exception;

class LoanInstallmentController
{
    use HasEnrichedRequest;


    public function updateStatus(Loan $loan, LoanInstallment $repayment) {
      if (request()->user()->current_team_id == $loan->team_id) {
        $repayment->updateStatus();
      }
    }

    public function update(Loan $loan, LoanInstallment $loanInstallment) {
      $updateData = $this->getPostData();
      return LoanInstallmentService::updateValues($updateData, $loanInstallment);
    }

    public function pay(Loan $loan, LoanInstallment $installment) {
      $postData = $this->getPostData();
      try {
        LoanTransactionsService::payRepayment($loan, $installment, $postData);
      } catch (Exception $e) {
        return response([
          "status" => 404,
          "error" => [
            "message" => $e->getMessage()
          ]
        ], 404);
      }
    }
}
