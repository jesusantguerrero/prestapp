<?php

namespace App\Domains\Properties\Services;

use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\Rent;
use Exception;
use Illuminate\Support\Facades\DB;

class PropertyService {

    public static function createRent(mixed $rentData, mixed $schedule = null) {
      $property = Property::find($rentData['property_id'])->get();
      if (!$property || $property->status !== Property::STATUS_AVAILABLE) {
        throw new Exception('This property is not available at the time');
      }
      $rent = Rent::create($rentData);
      $rent->property->update('status', Property::STATUS_RENTED);
      return self::createDepositTransaction($rent);
    }

    public static function createDepositTransaction($rent) {
       return $rent->createTransaction([
        "total" => $rent->amount
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
