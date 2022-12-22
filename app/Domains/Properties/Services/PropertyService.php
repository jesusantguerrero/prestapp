<?php

namespace App\Domains\Properties\Services;

use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\Rent;
use Exception;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Invoice\Invoice;

class PropertyService {

    public static function createRent(mixed $rentData, mixed $schedule = null) {
      $property = Property::find($rentData['property_id']);
      if (!$property || $property->status !== Property::STATUS_AVAILABLE) {
        throw new Exception('This property is not available at the time');
      } else {
        $rent = Rent::create($rentData);
        $rent->property->update(['status' => Property::STATUS_RENTED]);
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

      return Invoice::createDocument([
          'concept' =>  $formData['concept'] ?? 'Factura de Renta',
          'description' => $formData['description'] ?? "Mensualidad {$rent->client->fullName}",
          'user_id' => $rent->user_id,
          'team_id' => $rent->team_id,
          'client_id' => $rent->client_id,
          'invoiceable_id' => $rent->id,
          'invoiceable_type' => Rent::class,
          'date' => $formData['date'] ?? date('Y-m-d'),
          'type' => Invoice::DOCUMENT_TYPE_INVOICE,
          'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
          'total' =>  $formData['amount'] ?? $rent->amount,
          'items' => array_merge($formData['items'] ?? $items,  $withExtraServices ? $additionalFees : [])
      ]);
    }

    public static function ofTeam($teamId, $status= Property::STATUS_AVAILABLE) {
      return Property::where([
        'team_id' =>  $teamId,
        'status' => $status])
      ->get();
    }

    public static function totalByStatusFor(int $teamId) {
        $properties = Property::where('team_id', $teamId)
        ->select(DB::raw('count(*) as total, status'))
        ->groupBy('status')
        ->get();

        return  collect(self::mapInStatus($properties->toArray()))->mapWithKeys(function ($item) {
          return [$item['status'] => $item['total']];
      });;
    }

    public static function mapInStatus($results) {
      $status = [Property::STATUS_BUILDING, Property::STATUS_AVAILABLE, Property::STATUS_RENTED, Property::STATUS_MAINTENANCE];

      return array_map(function ($state) use ($results) {
          $index = array_search($state, array_column($results, 'status'));

          return  $index !== false ? $results[$index] : [
              "total" => 0,
              "status" => $state,
          ];
      }, $status);
    }
}
