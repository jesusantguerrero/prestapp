<?php

namespace App\Domains\Loans\Actions;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use App\Notifications\LateFeesGenerated;

class UpdateLatePayments {

    public static function run() {
        $today = date('Y-m-d');
        $lateInstallments = LoanInstallment::whereRaw('loan_installments.amount_paid < loan_installments.amount')
        ->where('due_date', '<', $today)
        ->where('loan_installments.late_fee', '=', 0)
        ->whereRaw('loans.late_fee', '>',  0)
        ->join('loans', 'loans.id', 'loan_id')
        ->get();

        if ($lateCount = count($lateInstallments)) {
          self::updateLatePayments($lateInstallments);
          $lateInstallments->first()->loan->user->notify(new LateFeesGenerated($lateCount));
        }
    }

    public static function updateLatePayments($payments) {
        foreach ($payments as $payment) {
            $penaltyAmount = 0;

            if ($payment->loan->late_fee_type == 'PERCENTAGE') {
                $penaltyAmount = ($payment->loan->late_fee / 100) * $payment->amount_due;
            } else {
                $penaltyAmount = $payment->loan->late_fee;
            }

            $payment->update([
              'late_fee' => $penaltyAmount,
              'amount' => $payment->amount + $penaltyAmount
            ]);

            $payment->loan->update([
              'payment_status' => Loan::STATUS_LATE
            ]);

            $payment->loan->client->checkStatus();
        }
    }
}
