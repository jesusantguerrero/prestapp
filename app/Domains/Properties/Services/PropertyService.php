<?php

namespace App\Domains\Properties\Services;

use App\Domains\Properties\Models\Property;
use Illuminate\Support\Facades\DB;

class PropertyService {
    public static function createDisbursementTransaction($loan) {
       return $loan->createTransaction([
        "total" => $loan->amount
       ]);
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
