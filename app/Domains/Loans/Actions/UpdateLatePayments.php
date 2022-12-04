<?php

namespace App\Domains\Loans\Actions;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;

class UpdateLatePayments {

    public static function run() {
        $today = date('Y-m-d');
        $lateInstallments = LoanInstallment::whereRaw('amount_paid < amount')->where('due_date', '<', $today)->get();
        if (count($lateInstallments)) {
            self::updateLatePayments($lateInstallments);
        }
    }

    public static function updateLatePayments($payments) {
        foreach ($payments as $payment) {
            $penaltyAmount = 0;

            if ($payment->loan->penalty_type == 'PERCENTAGE') {
                $penaltyAmount = ($payment->loan->penalty / 100) * $payment->amount_due;
            } else {
                $penaltyAmount = $payment->loan->penalty;
            }

            $payment->update([
                'penalty' => $penaltyAmount,
                'amount' => $payment->amount + $penaltyAmount
            ]);

            $payment->loan->update([
                'payment_status' => Loan::STATUS_LATE
            ]);

            $payment->loan->client->checkStatus();
        }
    }
}
