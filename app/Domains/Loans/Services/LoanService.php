<?php 

namespace App\Domains\Loans\Services;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;

class LoanService {
    
    public static function createLoan(mixed $loanData, mixed $installments) {
        $loan = Loan::create($loanData);
        self::createInstallments($loan, $installments);
        return self::createDisbursementTransaction($loan);
    }

    public static function createInstallments(Loan $loan, mixed $installments) {
        // LoanInstallment::query()->where('loan_id', $loan->id)
        // ->unpaid()->delete();
        foreach ($installments as $item) {
            $loan->installments()->create([
                "team_id" => $loan->team_id,
                "user_id" => $loan->user_id,
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

    public static function createDisbursementTransaction($loan) {
       return $loan->createTransaction([
        "total" => $loan->amount
       ]);
    }
}