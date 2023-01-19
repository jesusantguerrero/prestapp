<?php

namespace App\Domains\Loans\Services;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;

class LoanTransactionsService {

    public static function createPayment(Loan $loan, mixed $paymentData) {
      Loan::calculateTotal($loan);
      $loan->createPayment($paymentData);
      $loan->client->checkStatus();
    }

    public static function pay(Loan $loan, mixed $paymentData) {

      $data = array_merge($paymentData, [
        "client_id" => $loan->client_id,
        "documents" => array_map(function ($document) {
          $document['payable_id'] = $document['id'];
          $document['payable_type'] = LoanInstallment::class;
          $document['amount'] = $document['payment'];
          return $document;
        }, $paymentData['documents'])
      ]);

      self::createPayment($loan, $data);
    }

    public static function payRepayment(Loan $loan, LoanInstallment $installment, mixed $postData) {
      self::createPayment($loan, array_merge($postData, [
            "client_id" => $loan->client_id,
            "documents" => [[
                "payable_id" => $installment->id,
                "payable_type" => LoanInstallment::class,
                "amount" => $postData['amount'],
                "amount_due" =>$installment->total - $postData['amount'],
                "amount_paid" => $postData['amount']
            ]]
        ]));
    }
}
