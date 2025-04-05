<?php

namespace App\Domains\Properties\Services;

use Exception;
use App\Domains\CRM\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;

class PropertyService {

    public static function createProperty(mixed $propertyData, mixed $units = []) {
      $property = Property::create($propertyData);
      foreach ($units as $index => $unit) {
        $property->units()->create(array_merge([
          'team_id' => $property->team_id,
          'user_id' => $property->user_id,
          'owner_id' => $property->owner_id,
          'index' => $index
        ], $unit));
      }
      return $property;
    }

    public static function addUnit(Property $property, mixed $unitData) {
      $property->units()->create($unitData);
    }

    public static function removeUnit(Property $property, PropertyUnit $unit) {
      if ($unit->team_id == auth()->user()->current_team_id && $unit->status == PropertyUnit::STATUS_RENTED) {
        throw new Exception(__("This unit is currently rented"));
      }
      if (Rent::where('unit_id', $unit->id)->count()) {
        throw new Exception(__("This unit is linked to a past rent"));
      }
      $unit->delete();
    }

    public static function ofTeam($teamId, $status= Property::STATUS_AVAILABLE) {
      return Property::where([
        'team_id' =>  $teamId,
        'status' => $status])
      ->with(['units'])->get();
    }

    public static function totalByStatusFor(int $teamId) {
        $properties = PropertyUnit::query()
        ->where([
          'property_units.team_id' => $teamId, 
        ])
        ->whereNotIn('properties.status', [Property::STATUS_ADMINISTRATION_FINISHED, Property::STATUS_MAINTENANCE])
        ->selectRaw('
        Sum(CASE WHEN property_units.status = ? THEN 1 ELSE 0 END) as status_available,
        Sum(CASE WHEN property_units.status = ? THEN 1 ELSE 0 END) as status_rented,
        Sum(CASE WHEN property_units.status = ? THEN 1 ELSE 0 END) as status_building,
        Sum(CASE WHEN property_units.status = ? THEN 1 ELSE 0 END) as status_maintenance,
        count(distinct property_units.property_id) as property_count', [
          Property::STATUS_AVAILABLE,
          Property::STATUS_RENTED,
          Property::STATUS_BUILDING,
          Property::STATUS_MAINTENANCE,
        ])
        ->join('properties', 'properties.id', '=', 'property_units.property_id')
        ->whereNotNull('properties.status')

        ->first();
        
        return [
          "units" => [
            "available" => $properties->status_available,
            "rented" => $properties->status_rented,
            "building" => $properties->status_building,
            "maintenance" => $properties->status_maintenance,
          ],
          "properties" => $properties->property_count,
        ];
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

    public static function hintUnit($unitId) {
      return PropertyUnit::find($unitId);
    }

    public static function hintClient($clientId) {
      return Client::find($clientId);
    }

    public function getListKpi($teamId) {
      $statuses = [
        PropertyUnit::STATUS_AVAILABLE,
        PropertyUnit::STATUS_RENTED,
      ];


      $stateRaw = [];
      foreach ($statuses as $status) {
        $stateRaw[] = "SUM(CASE WHEN status = '$status' THEN 1 ELSE 0 END) as $status";
      }

      $stateRaw = implode(",", $stateRaw);

      return PropertyUnit::where('team_id', $teamId)
      ->selectRaw("
        count(id) as TOTAL,
        $stateRaw
      ")
      ->first();
    }
}
