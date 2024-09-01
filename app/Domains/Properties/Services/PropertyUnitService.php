<?php

namespace App\Domains\Properties\Services;

use Exception;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;

class PropertyUnitService {
    public static function appendTo(Property $property, mixed $unitData) {
      $property->units()->create($unitData);
    }

    public static function removeFrom(Property $property, PropertyUnit $unit) {
      if ($unit->team_id == auth()->user()->current_team_id && $unit->status == PropertyUnit::STATUS_RENTED) {
        throw new Exception(__("This unit is currently rented"));
      }
      if ($property->id !== $unit->property_id) {
        throw new Exception(__("This unit is doen't belongs to this property"));
      }
      if (Rent::where('unit_id', $unit->id)->count()) {
        throw new Exception(__("This unit is linked to a past rent"));
      }
      $unit->delete();
    }

    public static function updateIn(PropertyUnit $unit, mixed $unitData) {
      $name = $unitData["name"] ?? "";
      $isRented = $unit->status == PropertyUnit::STATUS_RENTED;
      if ($unit->team_id == auth()->user()->current_team_id && $name == $unit->name && $isRented) {
        throw new Exception(__("This unit is currently rented"));
      }
      if ($isRented) {
        $unit->update(collect($unitData)->only(['name'])->all());
      } else {
        $unit->update($unitData);
      }
    }

    public static function withBadStatus($teamId) {
      return PropertyUnit::where('status', PropertyUnit::STATUS_RENTED)
      ->where('team_id', $teamId)
      ->whereDoesntHave('contract')
      ->with(['contract']);
    }

    public static function freeUnitsWithBadStatus($teamId) {
      $units = self::withBadStatus($teamId)->get();

      foreach ($units as $unit) {
        $unit->update(['status' => PropertyUnit::STATUS_AVAILABLE]);

        return activity()
        ->performedOn($unit)
        ->log("Admin fixed bad status for unit $unit->name");
      }
    }
}
