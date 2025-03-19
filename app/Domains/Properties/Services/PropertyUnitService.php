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
      echo count($units);

      foreach ($units as $unit) {
        $unit->update(['status' => PropertyUnit::STATUS_AVAILABLE]);

        activity()
        ->performedOn($unit)
        ->log("Admin fixed bad status for unit $unit->name of $unit->property_name");
      }
    }

    public static function updateStatus(PropertyUnit $unit, $status, $user = null) {
      if ($unit->currentRent && $status == PropertyUnit::STATUS_AVAILABLE) {
        throw new Exception(__("This unit is currently rented"));
      }

      if (!$unit->currentRent && $status == PropertyUnit::STATUS_RENTED) {
        throw new Exception(__("This unit doesn't have a rent"));
      }

      $oldStatus = $unit->status;
      $unit->update(['status' => $status]);

      activity()
      ->performedOn($unit)
      ->by($user)
      ->log("updated status for unit $unit->name of $unit->property_name from $oldStatus to $status");
    }

    public static function fixBadStatus(PropertyUnit $unit) {
      if ($unit->team_id == auth()->user()->current_team_id && $unit->status == PropertyUnit::STATUS_RENTED) {
        $rent = $unit->currentRent;
        if (!$rent) {
          self::updateStatus($unit, PropertyUnit::STATUS_AVAILABLE);
          activity()
          ->performedOn($unit)
          ->log("Admin fixed bad status for unit $unit->name of $unit->property_name");
        }
      }
    }

    public static function getUnitsRequiringAction($teamId) {
      $badRentedUnits = PropertyUnit::query()
          ->select([
              'property_units.id',
              'property_units.name', 
              'property_units.status',
              'properties.id as property_id',
              'properties.name as property_name',
              'clients.display_name as client_name',
              'clients.id as client_id',
              'rents.id as rent_id',
              'rents.status as rent_status',
              'rents.move_out_at',
              'rents.end_date',
              'last_invoice.due_date as last_invoice_date',
              'last_invoice.total as last_invoice_amount',
              'last_invoice.debt as last_invoice_debt',
              'last_invoice.status as last_invoice_status'
          ])
          ->join('properties', 'property_units.property_id', '=', 'properties.id')
          ->join('rents', function($join) {
              $join->on('property_units.id', '=', 'rents.unit_id')
                  ->whereRaw('rents.id = (
                      SELECT id FROM rents r2 
                      WHERE r2.unit_id = property_units.id
                      ORDER BY created_at DESC 
                      LIMIT 1
                  )');
          })
          ->leftJoin('clients', 'rents.client_id', '=', 'clients.id') // Changed to leftJoin in case client was deleted
          ->leftJoin('invoices as last_invoice', function($join) { // Changed to leftJoin to match client join
              $join->on('rents.id', '=', 'last_invoice.invoiceable_id')
                  ->where('last_invoice.invoiceable_type', Rent::class)
                  ->whereRaw('last_invoice.id = (
                      SELECT MAX(id) FROM invoices 
                      WHERE invoiceable_id = rents.id 
                      AND invoiceable_type = ?
                  )', [Rent::class]);
          })
          ->where('property_units.team_id', $teamId)
          ->where('property_units.status', PropertyUnit::STATUS_RENTED)
          ->whereIn('rents.status', [Rent::STATUS_EXPIRED, Rent::STATUS_CANCELLED])
          ->whereNotNull('rents.id')
          ->get();

        $badAvailableUnits = PropertyUnit::query()->select([
            'property_units.id',
            'property_units.name',
            'property_units.status',
            'properties.id as property_id',
            'properties.name as property_name',
            'clients.display_name as client_name',
            'rents.status as rent_status',
            'rents.move_out_at',
            'rents.end_date',
            'last_invoice.due_date as last_invoice_date',
            'last_invoice.total as last_invoice_amount',
            'last_invoice.debt as last_invoice_debt',
            'last_invoice.status as last_invoice_status'
        ])
        ->join('properties', 'property_units.property_id', '=', 'properties.id')
        ->join('rents', function($join) {
            $join->on('property_units.id', '=', 'rents.unit_id')
                ->whereIn('rents.status', [Rent::STATUS_ACTIVE]);
        })
        ->leftJoin('clients', 'rents.client_id', '=', 'clients.id')
        ->leftJoin('invoices as last_invoice', function($join) {
            $join->on('rents.id', '=', 'last_invoice.invoiceable_id')
                ->where('last_invoice.invoiceable_type', Rent::class)
                ->whereRaw('last_invoice.id = (
                    SELECT MAX(id) FROM invoices 
                    WHERE invoiceable_id = rents.id 
                    AND invoiceable_type = ?
                )', [Rent::class]);
        })
        ->where('property_units.team_id', $teamId)
        ->where('property_units.status', '!=', PropertyUnit::STATUS_RENTED)
        ->whereNotNull('rents.id')
        ->get();

        $units = $badRentedUnits->merge($badAvailableUnits);
        return $units;
  }
}


