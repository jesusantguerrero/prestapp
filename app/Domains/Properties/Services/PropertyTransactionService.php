<?php

namespace App\Domains\Properties\Services;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Rent;
use Illuminate\Support\Carbon;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Tax;
use Insane\Journal\Models\Invoice\Invoice;

class PropertyTransactionService {
    public static function getProRatedAmount(Rent $rent) {
      $amountPerDay = $rent->amount / 30;
      $daysUsed = Carbon::createFromFormat('Y-m-d', $rent->first_invoice_date)->diffInDays(Carbon::createFromFormat('Y-m-d', $rent->date));
      return $amountPerDay * abs($daysUsed);
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
          "name" => "Depositos de {$rent->client->fullName}",
          "concept" => "Depositos de {$rent->client->fullName}",
          "account_id" => $rent->client_account_id,
          "quantity" => 1,
          "price" => $rent->deposit,
          "amount" => $rent->deposit,
        ]]
      ];
      self::createInvoice($formData, $rent, false);
    }

    public static function generateFirstInvoice(Rent $rent) {
      $proRatedAmount = self::getProRatedAmount($rent);
      $items = [[
        "name" => "Factura de Renta",
        "concept" => "Factura de {$rent->client->fullName}",
        "quantity" => 1,
        "price" => $proRatedAmount,
        "amount" => $proRatedAmount,
      ]];

      self::createInvoice([
        "date" => $rent->first_invoice_date,
        "items" => $items
      ], $rent);

      $rent->update([
        'next_invoice_date' => InvoiceHelper::getNextDate($rent->first_invoice_date),
        'generated_invoice_dates' => [$rent->first_invoice_date]
      ]);
    }

    public static function createDepositRefund($rent, $formData) {
      //todo:  validate not to return more than owed
      $refundAccountId = Account::guessAccount($rent, ['General Prepayments', 'customer_prepayments']);
      $invoiceData = [
        "date" => $formData['date'] ?? date('Y-m-d'),
        "due_date" => $formData['date'] ?? date('Y-m-d'),
        "concept" => $formData['concept'],
        'category_type' => PropertyInvoiceTypes::DepositRefund,
        "description" => $formData['details'],
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

    public static function createExpense(Rent $rent, $formData) {
      $items = [[
        "name" => $formData['concept'],
        "concept" => $formData['details'],
        "quantity" => 1,
        "account_id" => $formData['account_id'],
        "price" => $formData['amount'],
        "amount" => $formData['amount'],
      ]];

      self::createInvoice([
        "date" => $formData['date'] ?? date('Y-m-d'),
        "due_date" => $formData['date'] ?? date('Y-m-d'),
        "concept" => "Factura reembolso de deposito",
        'category_type' => PropertyInvoiceTypes::UtilityExpense,
        'type' => Invoice::DOCUMENT_TYPE_BILL,
        "description" => "Devolución de deposito {$rent->client->display_name}",
        "total" => $formData['amount'],
        "invoice_account_id" => $formData['account_id'],
        "account_id" => Account::guessAccount($rent, [$rent->property->name, 'expected_payments_vendors']),
        "items" => $items
      ], $rent);
    }

    public static function createLateFees($invoices) {
      foreach ($invoices as $invoice) {
          $penaltyAmount = 0;

          if ($invoice->invoiceable->late_fee_type == 'PERCENTAGE') {
              $penaltyAmount = ($invoice->invoiceable->late_fee / 100) * $invoice->invoiceable->total;
          } else if ($invoice->invoiceable->late_fee_type == 'PERCENTAGE_OUTSTANDING') {
              $penaltyAmount = $invoice->invoiceable->debt;
          } else {
              $penaltyAmount = $invoice->invoiceable->late_fee;
          }

          $invoice->update([
            'status' => 'overdue'
          ]);

          $invoice->invoiceable->update([
            'status' => Rent::STATUS_LATE
          ]);

          PropertyTransactionService::createInvoice([
            "name" => "Factura de mora",
            "concept" => "Factura de mora {$invoice->invoiceable->client->fullName}",
            'invoice_account_id' => $invoice->invoiceable->late_fee_account_id,
            'total' => $penaltyAmount
          ], $invoice->invoiceable);

          $invoice->invoiceable->client->checkStatus();
      }
    }

    public static function createOwnerDistribution($client, $invoiceId = null) {
      if (!$invoiceId) {
        $invoices = $client->getPropertyInvoices();
      } else {
        $existingInvoice = $client->invoices()->where('id', $invoiceId)->with(['relatedChilds'])->first();
        $invoices = $client->getPropertyInvoices($existingInvoice->id);
      }

      $items = [];
      $total = 0;
      $taxTotal = 0;
      foreach ($invoices as $invoice) {
         $type = $invoice->category_type == PropertyInvoiceTypes::DepositRefund->value ? -1 : 1;
          $item = [
            "name" => "$invoice->description $invoice->date",
            "concept" => "$invoice->description $invoice->date",
            "quantity" => 1,
            "account_id" => $invoice->invoice_account_id,
            "price" => $type * $invoice->total,
            "amount" => $type * $invoice->total,
          ];

          if ($invoice->category_type == PropertyInvoiceTypes::Rent->value) {
            $rent = $invoice->invoiceable;
            $retention = Tax::guessRetention("Commission", $rent->commission, $invoice->toArray(), [
              "description" => 'Descuento de abogado',
            ]);

            $retentionTotal = (double) $retention->rate * $invoice->total / 100;

            $item['taxes'] = [
              [
                "id" => $retention->id,
                "name" => $retention->name,
                "concept" => $retention->description,
                "rate" => $retention->rate,
                "type" => $retention->type,
                "label" => $retention->label,
                "description" => $retention->description,
                "amount" => $retentionTotal,
                "amount_base" => $invoice->total,
                "index" => 1,
              ]
            ];

            $taxTotal += $retentionTotal * $retention->type;
          }
          $items[] = $item;
          $total += $invoice->total;

      }

      if (count($items)) {
        $today = date('Y-m-d');
        $clientProperty = $client->properties()->first();
        $documentData = [
          'concept' =>  $formData['concept'] ?? 'Factura de Propiedades',
          'description' => $formData['description'] ?? "Mensualidad {$client->fullName}",
          'user_id' => $client->user_id,
          'team_id' => $client->team_id,
          'client_id' => $client->id,
          'invoiceable_id' => $client->id,
          'invoiceable_type' => Client::class,
          'invoice_account_id' => $clientProperty->owner_account_id,
          'date' => $formData['date'] ?? date('Y-m-d'),
          'type' => Invoice::DOCUMENT_TYPE_BILL,
          'category_type' => PropertyInvoiceTypes::OwnerDistribution,
          'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
          'total' =>  $formData['amount'] ?? $total,
          'items' => $formData['items'] ?? $items,
          'related_invoices' => [[
              "name" => PropertyInvoiceTypes::OwnerDistribution->name(),
              "items" => $invoices->map(function($invoice) {
                  return [
                    "id" => $invoice->id,
                    "description" => $invoice->category_type
                  ];
              })
            ]
          ]
      ];

      if (isset($existingInvoice)) {
        $existingInvoice->updateDocument($documentData);
      } else {
        Invoice::createDocument($documentData);
      }

      $client->update([
        'generated_distribution_dates' => array_merge($client->generated_distribution_dates, [$today])
      ]);
      }
    }
}
