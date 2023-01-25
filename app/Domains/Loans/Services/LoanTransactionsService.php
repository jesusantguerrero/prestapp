<?php

namespace App\Domains\Loans\Services;

use App\Domains\Loans\Enums\LoanInvoiceTypes;
use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;

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

    public static function payoff(Loan $loan, mixed $paymentData) {
      $repayments = $loan->installments()->unpaid()->selectRaw('id payable_id, amount_due amount, ? as payable_type', [LoanInstallment::class])->get();

      $paymentData['amount'] = $repayments->sum('amount');
      $paymentData['concept'] = 'Saldo de prestamo';

      $data = array_merge($paymentData, [
        "client_id" => $loan->client_id,
        "documents" => $repayments->toArray()
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

    public static function createAgreement(Loan $loan, mixed $formData) {
      $items = [[
        "name" => "Acuerdo de pago",
        "concept" => "Prestamo #{$loan->id} {$loan->client->fullName}",
        "quantity" => 1,
        "price" => $formData['amount'] ?? $formData['debt'],
        "amount" => $formData['amount'] ?? $formData['debt'],
      ]];

      $data = array_merge($formData, [
        'concept' =>  'Acuerdo de pago',
        'description' =>"Acuerdo de prestamo {$loan->id} {$loan->client->fullName}",
        'user_id' => $loan->user_id,
        'team_id' => $loan->team_id,
        'client_id' => $loan->client_id,
        'invoiceable_id' => $loan->id,
        'invoiceable_type' => Loan::class,
        'date' => $formData['date'] ?? date('Y-m-d'),
        'type' => $formData['type'] ?? Invoice::DOCUMENT_TYPE_INVOICE,
        'category_type' => $formData['category_type'] ?? LoanInvoiceTypes::PaymentAgreement,
        "invoice_account_id" => $formData["invoice_account_id"] ?? $loan->client_account_id,
        "account_id" => $formData["account_id"] ?? Account::guessAccount($loan, ['Acuerdos de pago', 'expected_payments_loans']),
        'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
        'total' =>  $formData['amount'] ?? $formData['debt'],
        'items' => $items ?? [],
      ]);

      return Invoice::createDocument($data);
    }

    public static function close(Loan $loan, $data) {
      $debt = $loan->installments()->sum('amount_due');

      LoanService::cancel($loan, $data['reason'] ."::$debt", $data['date'], 'closed');
    }
}
