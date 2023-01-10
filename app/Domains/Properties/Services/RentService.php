<?php

namespace App\Domains\Properties\Services;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use Exception;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Invoice\Invoice;

class RentService {
    public static function createRent(mixed $rentData, mixed $schedule = null) {
      $unit = PropertyUnit::find($rentData['unit_id']);
      if (!$unit || $unit->status !== PropertyUnit::STATUS_AVAILABLE) {
        throw new Exception('This unit is not available at the time');
      } else {
        $rentData =
        array_merge($rentData, [
          'account_id' => $unit->property->account_id,
          'owner_id' => $unit->property->owner_id,
          'commission_account_id' => $unit->property->commission_account_id,
          'late_fee_account_id' => $unit->property->late_fee_account_id,
        ]);
        $rent = Rent::create($rentData);
        $rent->unit->update(['status' => PropertyUnit::STATUS_RENTED]);
        return self::createDepositTransaction($rent, $rentData);
      }
    }

    public static function allowedUpdate(mixed $rentData) {
      $validData = [];
      $cantUpdate = collect([
        'rent_id',
        'property_id'
      ]);

      foreach ($rentData as $key => $value) {
        if (!$cantUpdate->contains($key)) {
          $validData[$key] = $value;
        }
      }
      return $validData;
    }

    public static function createDepositTransaction($rent, $rentData) {
      $formData = [
        "date" => $rent->deposit_due,
        "due_date" => $rent->deposit_due,
        "concept" => "Factura Déposito",
        "description" => "Déposito de {$rent->client->fullName}",
        "total" => $rent->deposit,
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
      $invoiceData = [
        "date" => $rent->deposit_due,
        "due_date" => $rent->deposit_due,
        "concept" => "Factura reembolso de deposito",
        'category_type' => PropertyInvoiceTypes::DepositRefund,
        "description" => "Devolución de deposito {$rent->client->display_name}",
        "total" => $formData['total'],
        "invoice_account_id" => $formData['account_id'],
        "items" => [],
        "relatedInvoices" => [
          "name" => PropertyInvoiceTypes::DepositRefund->value,
          "items" => []
        ]
      ]; // liability to client here general

      foreach ($formData['payments'] as $payment) {
          $invoiceData['items'][] = [
            "name" => "Depositos de $rent->address",
            "concept" => "Depositos de $rent->address",
            "quantity" => 1,
            "account_id" => $rent->property->deposit_account_id,
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
        'category_type' => $formData['category_type'] ?? PropertyInvoiceTypes::Rent->name(),
        "invoice_account_id" => $rent->property->account_id,
        'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
        'total' =>  $formData['amount'] ?? $rent->amount,
        'items' => array_merge($formData['items'] ?? $items,  $withExtraServices ? $additionalFees : []),
        "related_invoices" => $formData["related_invoices"] ?? []
      ]);

      return Invoice::createDocument($data);
    }

    public static function endTerm(Rent $rent, $formData) {
      if ($rent->status !== Rent::STATUS_CANCELLED) {
        $rent->update(array_merge($formData, ["status" => Rent::STATUS_CANCELLED]));
        $rent->property->update(['status' => Property::STATUS_AVAILABLE]);
      }
      throw new Exception('Rent is already cancelled');
    }

    //  payments / invoices
    public static function invoices($teamId, $statuses = []) {
      $query = Invoice::selectRaw('clients.names contact, clients.id contact_id, invoices.debt, invoices.due_date, invoices.id id, invoices.concept')
        ->where([
          'invoices.team_id' => $teamId,
          'invoices.type' => 'INVOICE',
          'invoiceable_type' => Rent::class
        ]);

        if (count($statuses)) {
          $query->whereIn('invoices.status', $statuses);
        }

        $query
        ->join('clients', 'clients.id', '=', 'invoices.client_id')
        ->groupBy(['clients.names', 'clients.id', 'invoices.debt', 'invoices.due_date', 'invoices.id', 'invoices.concept'])
        ->take(5);

        return $query;
    }

    public static function nextInvoices($teamId, $status = 'unpaid') {
      return self::invoices($teamId, [$status])->get();
    }

    public static function generateNextInvoice($rent) {
      RentService::createInvoice([
        'date' => $rent->next_invoice_date
      ], $rent);
        $rent->update([
          'next_invoice_date' => InvoiceHelper::getNextDate($rent->next_invoice_date),
          'generated_invoice_dates' => array_merge($rent->generated_invoice_dates, [$rent->next_invoice_date])
        ]);
    }
}
