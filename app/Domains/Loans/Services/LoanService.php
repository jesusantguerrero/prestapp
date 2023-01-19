<?php

namespace App\Domains\Loans\Services;

use App\Domains\Loans\Jobs\CreateLoanTransaction;
use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;

class LoanService {

    public static function createLoan(mixed $loanData, mixed $installments) {
        $loan = Loan::create(array_merge($loanData, [
          'status' => Loan::STATUS_DISPOSED,
        ]));
        self::createInstallments($loan, $installments);
        CreateLoanTransaction::dispatch($loan);
    }

    public static function updateLoan(mixed $loanData, Loan $loan) {
      $loan->update($loanData);
      CreateLoanTransaction::dispatch($loan);
    }

    public static function createInstallments(Loan $loan, mixed $installments) {
        LoanInstallment::where([
            'loan_id' => $loan->id,
            'payment_status' => LoanInstallment::STATUS_PENDING
        ])->delete();
        foreach ($installments as $item) {
            $loan->installments()->create([
                "team_id" => $loan->team_id,
                "user_id" => $loan->user_id,
                "client_id" => $loan->client_id,
                "due_date" => $item["due_date"],
                "installment_number" => $item["installment_number"],
                "initial_balance" => $item["initial_balance"],
                "amount" => $item["amount"],
                "interest" => $item["interest"],
                "principal" => $item["principal"],
                "final_balance" => $item["final_balance"],
                "status" => LoanInstallment::STATUS_PENDING,
            ]);
        }

        $loan->save();
        return $loan;
    }

    public static function disposedCapitalFor(int $teamId) {
        return Loan::where('team_id', $teamId)->sum('amount');
    }

    public static function expectedInterestFor(int $teamId) {
        return LoanInstallment::where('team_id', $teamId)->sum('interest');
    }

    public static function paidInterestFor(int $teamId) {
        return LoanInstallment::where('team_id', $teamId)->sum('interest_paid');
    }

    public static function invoices(int $teamId, int $clientId = null) {
        return LoanInstallment::byTeam($teamId);
        // ->byClient($clientId);
    }

    public static function getStats(Loan $loan) {
      return $loan->installments()->selectRaw("sum(principal - principal_paid) as outstandingPrincipal, sum(interest - interest_paid) as outstandingInterest,sum(late_fee - late_fee_paid) as outstandingFees,sum(late_fee - late_fee_paid) as outstandingFees,sum(amount_due) as outstandingTotal
      ")->first();
    }
}
