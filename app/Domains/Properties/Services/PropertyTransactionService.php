<?php

namespace App\Domains\Properties\Services;

use Exception;
use Illuminate\Support\Carbon;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Helpers\ReportHelper;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Core\Services\AuditService;
use App\Domains\Properties\Models\Property;
use Insane\Journal\Models\Invoice\InvoiceNote;
use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Actions\PropertyTransactionsValidator;

class PropertyTransactionService {
    public static function getProRatedAmount(Rent $rent) {
      $amountPerDay = $rent->amount / 30;
      $daysUsed = Carbon::createFromFormat('Y-m-d', $rent->first_invoice_date)->diffInDays(Carbon::createFromFormat('Y-m-d', $rent->date));
      return $daysUsed < 30 ? $amountPerDay * abs($daysUsed) : $rent->amount;
    }

    public static function createPropertyInvoice($formData, Property $property, $withExtraServices = true) {
      $amountDue =  $formData['total'] ?? $formData['amount'];
      $additionalFees =  [];
      $items = [[
        "name" => "Factura de Renta",
        "concept" => "Factura de {$property->name}",
        "quantity" => 1,
        "price" => $amountDue,
        "amount" => $amountDue,
      ]];

      $data = array_merge($formData, [
        'concept' =>  $formData['concept'],
        'description' => $formData['description'],
        'user_id' => $property->user_id,
        'team_id' => $property->team_id,
        'client_id' => $property->owner_id,
        'invoiceable_id' => $property->id,
        'invoiceable_type' => Property::class,
        'date' => $formData['date'] ?? date('Y-m-d'),
        'type' => $formData['type'] ?? Invoice::DOCUMENT_TYPE_INVOICE,
        'category_type' => $formData['category_type'] ?? PropertyInvoiceTypes::Charge,
        "invoice_account_id" => $formData["invoice_account_id"] ?? $property->account_id,
        "account_id" => $formData["account_id"] ?? $property->owner_account_id,
        'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
        'total' =>  $amountDue,
        'items' => array_merge($formData['items'] ?? $items,  $withExtraServices ? $additionalFees : []),
        "related_invoices" => $formData["related_invoices"] ?? []
      ]);

      if (isset($formData['payment_details'])) {
        $data['payment_details'] = $formData['payment_details'];
      }

      if (isset($formData['is_paid']) && $formData['is_paid']) {
        $data['payment_details'] = [
          'account_id' => $property->account_id,
          'concept' => "Pago {$data['concept']}",
          'payment_method' => $data['payment_method'] ?? 'cash'
        ];
      }


      return Invoice::createDocument($data);
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
        'description' => $formData['description'] ?? "{$rent->unit->name} Mensualidad {$rent->client->fullName}",
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

      if (isset($formData['is_paid']) && $formData['is_paid']) {
        $data['payment_details'] = [
          'account_id' => $rent->property->account_id,
          'concept' => "Pago {$data['concept']}",
          'payment_method' => $data['payment_method'] ?? 'cash'
        ];
      }

      return Invoice::createDocument($data);
    }

    public static function createDepositTransaction(Rent $rent, mixed $rentData) {
      $description = "{$rent->unit->name} Déposito de {$rent->client->fullName}";

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
          'account_id' => Account::findByDisplayId('real_state', $rent->team_id)->id,
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

    public static function applyDepositTo($rent, $parentInvoice, $formData) {
      (new PropertyTransactionsValidator())->canRefund($rent->client, $formData['total']);
        $refundAccountId = Account::guessAccount($rent, ['real_state', 'cash_and_bank']);

        $concept = "Aplicacion de deposito $rent->client_name";
        $invoiceData = [
          "date" => $formData['date'] ?? date('Y-m-d'),
          "due_date" => $formData['date'] ?? date('Y-m-d'),
          "concept" => "Factura aplicacion de deposito",
          'category_type' => PropertyInvoiceTypes::DepositApply,
          "type" => Invoice::DOCUMENT_TYPE_CREDIT_NOTE,
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

        $invoiceData['items'][] = [
          "name" => $parentInvoice->concept,
          "concept" => $invoiceData['concept'],
          "quantity" => 1,
          "account_id" => $rent->property->deposit_account_id,
          "category_id" => $rent->client_account_id ,
          "price" =>   $formData['total'],
          "amount" =>  $formData['total'],
        ];

        if (isset($formData['payment_details'])) {
          $invoiceData['payment_details'] = array_merge(
            $formData['payment_details'],
          [
            "concept" => "Pago {$invoiceData['concept']}"
          ]);
        }

        $invoice = self::createInvoice($invoiceData, $rent, false);

        $parentInvoice->applyNote(
          $invoice->id,
          InvoiceNote::TYPE_CREDIT,
          $formData['total'],
          $invoice->date
        );
        return $invoice;

    }

    public static function createOrUpdateExpense(Property $property, $formData, $invoiceId = null) {

      $vendorAccountId = Account::guessAccount($property, [$property->name, 'expected_payments_vendors']);
      $expenseAccountId = Account::guessAccount($property, ['General Expenses', 'expenses'], [
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
        "description" => "{$formData['concept']} {$property->name}",
        "total" => $formData['amount'],
        "invoice_account_id" => $vendorAccountId, // fallback credit account in case that items doesn't have an account_id and default payment account
        "account_id" => $expenseAccountId, // debit account
        "items" => $items,
      ];

      if (isset($formData['is_paid_expense']) && $formData['is_paid_expense']) {
        $invoiceData['payment_details'] = [
          'account_id' => $formData['payment_account_id'] ?? $property->account_id,
          'concept' => "Pago {$formData['concept']}",
          'payment_method' => $formData['payment_method'] ?? 'cash'
        ];
      }

      if ($invoiceId) {
        $invoice = Invoice::find($invoiceId);
        $invoice->updateDocument($invoiceData);
        return $invoice;
      } else {
        return self::createPropertyInvoice($invoiceData, $property);
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
        self::createLateFee($invoice->invoiceable, [], $invoice);
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
          'status' => Invoice::STATUS_OVERDUE
        ]);

        $invoice->invoiceable->update([
          'status' => Rent::STATUS_LATE
        ]);
      }

      $amount =  $formData['amount'] ?? $penaltyAmount;

      $lateFeeInvoice = PropertyTransactionService::createInvoice([
        "concept" => "Factura de mora",
        "description" => $formData['concept'] ?? "{$rent->unit} Mora {$rent->client->fullName}",
        'invoice_account_id' => $rent->late_fee_account_id,
        'amount' => $amount,
        'due_date' => $formData['due_date'] ?? null,
        'category_type' => PropertyInvoiceTypes::LateFee,
        "items" => [[
            "name" => "Mora de renta",
            "concept" => $formData['concept'] ?? "{$rent->unit} Mora de {$rent->client->fullName}",
            "quantity" => 1,
            "price" => $amount,
            "amount" => $amount,
        ]]
      ], $rent);

      $rent->client->checkStatus();
      AuditService::dispatchCustomEvent(
        $rent,
        AuditService::RENT_LATE_INVOICE_NEW,
        $lateFeeInvoice->toArray()
      );
    }

    public static function createOwnerDistribution($client, $invoiceId = null) {
      $ownerService = new OwnerDistributionService($client);
      if (!$invoiceId) {
       $ownerService->fromAutomation();
      } else {
        $ownerService->regenerateFromRequest($invoiceId);
      }
    }

    public static function createOwnerDistributionAuto($client, $invoiceId = null) {
      $ownerService = new OwnerDistributionService($client);
      if (!$invoiceId) {
       $ownerService->fromAutomation();
      } else {
        $ownerService->updateFromAutomation($invoiceId);
      }
    }
}
