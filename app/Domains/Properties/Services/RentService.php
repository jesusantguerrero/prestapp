<?php

namespace App\Domains\Properties\Services;

use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use Exception;
use Insane\Journal\Models\Invoice\Invoice;

class RentService {
    public static function createRent(mixed $rentData, mixed $schedule = null) {
      $unit = Property::find($rentData['unit_id']);
      if (!$unit || $unit->status !== PropertyUnit::STATUS_AVAILABLE) {
        throw new Exception('This unit is not available at the time');
      } else {
        $rentData =
        array_merge($rentData, [
          'account_id' => $unit->property->account_id,
          'owner_id' => $unit->property->owner_id,
          'commission_account_id' => $unit->property->commission_account_id,
          'late_fee_account_id' => $$unit->property->late_fee_account_id,
        ]);
        $rent = Rent::create($rentData);
        $rent->unit->update(['status' => PropertyUnit::STATUS_RENTED]);
        return self::createDepositTransaction($rent);
      }
    }

    public static function createDepositTransaction($rent) {
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
          "quantity" => 1,
          "price" => $rent->deposit,
          "amount" => $rent->deposit,
        ]]
      ];
      return self::createInvoice($formData, $rent, false);
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
        "invoice_account_id" => $rent->property->account_id,
        'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
        'total' =>  $formData['amount'] ?? $rent->amount,
        'items' => array_merge($formData['items'] ?? $items,  $withExtraServices ? $additionalFees : [])
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
}
