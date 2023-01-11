<?php

namespace App\Domains\Properties\Services;

use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;

class PropertyTransactionService {
    public static function createDepositTransaction($rent, $rentData) {
      $formData = [
        "date" => $rent->deposit_due,
        "due_date" => $rent->deposit_due,
        "concept" => "Factura Déposito",
        "description" => "Déposito de {$rent->client->fullName}",
        "total" => $rent->deposit,
        'category_type' => PropertyInvoiceTypes::Deposit,
        "invoice_account_id" => $rent->property->deposit_account_id,
        "items" => [[
          "name" => "Depositos de $rent->address",
          "concept" => "Depositos de $rent->address",
          "account_id" => $rent->client_account_id,
          "quantity" => 1,
          "price" => $rent->deposit,
          "amount" => $rent->deposit,
        ]]
      ];
      self::createInvoice($formData, $rent, false);
    }

    public static function createDepositRefund($rent, $formData) {
      //todo:  validate not to return more than owed
      $refundAccountId = Account::guessAccount($rent, ['General Prepayments', 'customer_prepayments']);
      $invoiceData = [
        "date" => $formData['date'] ?? date('Y-m-d'),
        "due_date" => $formData['date'] ?? date('Y-m-d'),
        "concept" => "Factura reembolso de deposito",
        'category_type' => PropertyInvoiceTypes::DepositRefund,
        "description" => "Devolución de deposito {$rent->client->display_name}",
        "total" => $formData['total'],
        "invoice_account_id" => $formData['account_id'],
        "account_id" => $refundAccountId,
        "items" => [],
        "relatedInvoices" => [
          "name" => PropertyInvoiceTypes::DepositRefund,
          "items" => []
        ]
      ];

      foreach ($formData['payments'] as $payment) {
          $invoiceData['items'][] = [
            "name" => "Rembolso de depositos de {$rent->client->display_name}",
            "concept" => "Rembolso de depositos de {$rent->client->display_name}",
            "quantity" => 1,
            "account_id" => $refundAccountId,
            "price" => $payment['amount'],
            "amount" => $payment['amount'],
          ];
          $invoiceData["relatedInvoices"]['items'][] =[
            "id" => $payment['id'],
            "description" => PropertyInvoiceTypes::Deposit
          ];
      }

      return self::createInvoice($invoiceData, $rent, false);
    }

    public static function createInvoice($formData, Rent $rent, $withExtraServices = true) {
      $additionalFees =  $rent->services ?? [];
      $items = [[
        "name" => "Factura de Renta",
        "concept" => "Factura de {$rent->client->fullName}",
        "quantity" => 1,
        "price" => $rent->amount,
        "amount" => $rent->amount,
      ]];

      $data = array_merge($formData, [
        'concept' =>  $formData['concept'] ?? 'Factura de Renta',
        'description' => $formData['description'] ?? "Mensualidad {$rent->client->fullName}",
        'user_id' => $rent->user_id,
        'team_id' => $rent->team_id,
        'client_id' => $rent->client_id,
        'invoiceable_id' => $rent->id,
        'invoiceable_type' => Rent::class,
        'date' => $formData['date'] ?? date('Y-m-d'),
        'type' => Invoice::DOCUMENT_TYPE_INVOICE,
        'category_type' => $formData['category_type'] ?? PropertyInvoiceTypes::Rent,
        "invoice_account_id" => $formData["invoice_account_id"] ??$rent->property->account_id,
        'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
        'total' =>  $formData['amount'] ?? $rent->amount,
        'items' => array_merge($formData['items'] ?? $items,  $withExtraServices ? $additionalFees : []),
        "related_invoices" => $formData["related_invoices"] ?? []
      ]);

      return Invoice::createDocument($data);
    }
}
