<?php

namespace App\Domains\Properties\Services;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Actions\PropertyTransactionsValidator;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Rent;
use Illuminate\Support\Carbon;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;

class PropertyTransactionService {
    public static function getProRatedAmount(Rent $rent) {
      $amountPerDay = $rent->amount / 30;
      $daysUsed = Carbon::createFromFormat('Y-m-d', $rent->first_invoice_date)->diffInDays(Carbon::createFromFormat('Y-m-d', $rent->date));
      return $daysUsed < 30 ? $amountPerDay * abs($daysUsed) : $rent->amount;
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
        'type' => $formData['type'] ?? Invoice::DOCUMENT_TYPE_INVOICE,
        'category_type' => $formData['category_type'] ?? PropertyInvoiceTypes::Rent,
        "invoice_account_id" => $formData["invoice_account_id"] ?? $rent->property->account_id,
        "account_id" => $formData["account_id"] ??$rent->property->client_account_id,
        'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
        'total' =>  $formData['total'] ?? $formData['amount'] ?? $rent->amount,
        'items' => array_merge($formData['items'] ?? $items,  $withExtraServices ? $additionalFees : []),
        "related_invoices" => $formData["related_invoices"] ?? []
      ]);

      if (isset($formData['payment_details'])) {
        $data['payment_details'] = $formData['payment_details'];
      }

      return Invoice::createDocument($data);
    }

    public static function createDepositTransaction(Rent $rent, mixed $rentData) {
      $description = "Déposito de {$rent->client->fullName}";

      $formData = [
        "date" => $rent->deposit_due,
        "due_date" => $rent->deposit_due,
        "concept" => "Factura Déposito",
        "description" => $description,
        "total" => $rent->deposit,
        'category_type' => PropertyInvoiceTypes::Deposit,
        "invoice_account_id" => $rent->property->deposit_account_id,
        "account_id" => $rent->client_account_id,
        "items" => [[
          "name" => "Depositos de {$rent->client->fullName}",
          "concept" => "Depositos de {$rent->client->fullName}",
          "account_id" => $rent->property->deposit_account_id,
          "quantity" => 1,
          "price" => $rent->deposit,
          "amount" => $rent->deposit,
        ]]
      ];

      if (isset($rentData['is_deposit_received'])) {
        $formData['payment_details'] = [
          'account_id' => Account::findByDisplayId('real_state', $rent->team_id),
          'concept' => "Pago $description",
          'payment_method' => $formData['payment_method'] ?? 'cash'
        ];
      }
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

    public static function createDepositRefund($rent, $formData, $storedInvoice = null) {
      (new PropertyTransactionsValidator())->canRefund($rent->client, $formData['total']);

      $refundAccountId = Account::guessAccount($rent, ['real_state', 'cash_and_bank']);
      $concept = "Devolucion de deposito $rent->client_name";
      $invoiceData = [
        "date" => $formData['date'] ?? date('Y-m-d'),
        "due_date" => $formData['date'] ?? date('Y-m-d'),
        "concept" => "Factura reembolso de deposito",
        'category_type' => PropertyInvoiceTypes::DepositRefund,
        "type" => Invoice::DOCUMENT_TYPE_BILL,
        "description" => $concept,
        "amount" => $formData['total'],
        "total" => $formData['total'],
        "account_id" => $rent->property->deposit_account_id,
        "invoice_account_id" => $rent->client_account_id,
        "items" => [],
        'payment_details' => [
          'account_id' => $refundAccountId,
          'concept' => "Pago $concept",
          'payment_method' => $formData['payment_method'] ?? 'cash'
        ]
      ];

      foreach ($formData['payments'] as $payment) {
          $invoiceData['items'][] = [
            "name" => "Reembolso de depositos de {$rent->client->display_name}",
            "concept" => "Reembolso de depositos de {$rent->client->display_name}",
            "quantity" => 1,
            "account_id" => $rent->property->deposit_account_id,
            "category_id" => $rent->client_account_id ,
            "price" => $payment['amount'],
            "amount" => $payment['amount'],
          ];

          $invoiceData["relatedInvoices"]['items'][] =[
            "id" => $payment['id'],
            "description" => PropertyInvoiceTypes::Deposit
          ];
      }


      if (isset($formData['payment_details'])) {
        $invoiceData['payment_details'] = array_merge(
          $formData['payment_details'],
        [
          "concept" => "Pago {$invoiceData['concept']}"
        ]);
      }

      if (!$storedInvoice) {
        $invoice = self::createInvoice($invoiceData, $rent, false);
      } else {
        $invoice = $storedInvoice->updateDocument($invoiceData);

      }

      return $invoice;
    }

    public static function createOrUpdateExpense(Rent $rent, $formData, $invoiceId = null) {
      $vendorAccountId = Account::guessAccount($rent, [$rent->property->name, 'expected_payments_vendors']);
      $expenseAccountId = Account::guessAccount($rent, ['General Expenses', 'expenses'], [
        'alias' => 'Gastos Generales'
      ]);

      $items = [[
        "name" => $formData['concept'],
        "concept" => $formData['details'],
        "quantity" => 1,
        'category_id' => $vendorAccountId,
        'account_id' => $expenseAccountId,
        "price" => $formData['amount'],
        "amount" => $formData['amount'],
      ]];

      $invoiceData = [
        "date" => $formData['date'] ?? date('Y-m-d'),
        "due_date" => $formData['date'] ?? date('Y-m-d'),
        "concept" => $formData['concept'],
        'category_type' => PropertyInvoiceTypes::UtilityExpense,
        'type' => Invoice::DOCUMENT_TYPE_BILL,
        "description" => "{$formData['concept']} {$rent->client->display_name}",
        "total" => $formData['amount'],
        "invoice_account_id" => $vendorAccountId, // fallback credit account in case that items doesn't have an account_id and default payment account
        "account_id" => $expenseAccountId, // debit account
        "items" => $items
      ];

      if ($invoiceId) {
        Invoice::find($invoiceId)->updateDocument($invoiceData);
      } else {
        self::createInvoice($invoiceData, $rent);
      }
    }

    public static function getInvoiceLineType(string $invoiceType) {
      $type = 1;
      switch ($invoiceType) {
        case PropertyInvoiceTypes::UtilityExpense->value:
        case PropertyInvoiceTypes::DepositRefund->value:
          $type = -1;
          break;
      }

      return $type;
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

    public static function createLateFee(Rent $rent, $formData = [], $invoice = null) {
          $penaltyAmount = 0;

          if ($rent->late_fee_type == 'PERCENTAGE') {
              $penaltyAmount = ($rent->late_fee / 100) * $rent->total;
          } else if ($rent->late_fee_type == 'PERCENTAGE_OUTSTANDING') {
              $penaltyAmount = $rent->debt;
          } else {
              $penaltyAmount = $rent->late_fee;
          }

          if ($invoice) {
            $invoice->update([
              'status' => 'overdue'
            ]);

            $invoice->invoiceable->update([
              'status' => Rent::STATUS_LATE
            ]);
          }

          $amount =  $formData['amount'] ?? $penaltyAmount;

          PropertyTransactionService::createInvoice([
            "name" => "Factura de mora",
            "concept" => $formData['concept'] ?? "Factura de mora {$rent->client->fullName}",
            'invoice_account_id' => $rent->late_fee_account_id,
            'amount' => $amount,
            'due_date' => $formData['due_date'] ?? null,
            'category_type' => PropertyInvoiceTypes::LateFee,
            "items" => [[
                "name" => "mora de renta",
                "concept" => $formData['concept'] ?? "mora de {$rent->client->fullName}",
                "quantity" => 1,
                "price" => $amount,
                "amount" => $amount,
            ]]
          ], $rent);

          $rent->client->checkStatus();
    }

    public static function createOwnerDistribution($client, $invoiceId = null) {
      $ownerService = new OwnerDistributionService($client);
      if (!$invoiceId) {
       $ownerService->fromAutomation();
      } else {
        $ownerService->updateFromAutomation($invoiceId);
      }
    }
}
