<?php

namespace App\Http\Controllers\Loans;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Services\LoanService;
use App\Domains\Loans\Services\LoanTransactionsService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LoanAgreementController extends Controller
{
   public function store(Loan $loan) {
    $postData = request()->post();
    if ($postData && $loan->team_id == request()->user()->current_team_id) {
        if ($loan->payment_status !== Loan::STATUS_PAID) {
            $unpaidRepayments = $loan->installments()->unpaid()->count();
            $postData['debt'] = $loan::query()->sum(DB::raw('total - amount_paid'));
            if ($unpaidRepayments != 0) {
                DB::transaction(function () use ($postData, $loan) {
                    LoanService::cancel($loan, $postData['reason'], $postData['date']);
                    LoanTransactionsService::createAgreement($loan, $postData);
                });
            } else {
                return back()->withErrors([
                  "message" => [
                    "type" => "error",
                    "text" => "Ya hay pagos para esta fecha"
                  ],
                  "data" => []
                ]);
            }
        } else {
            return back()->withErrors([
              "message" => [
                "type" => "error",
                "text" => "El contrato debe estar en estado suspendido"
              ],
              "data" => []
            ]);
        }
    }
  }
}
