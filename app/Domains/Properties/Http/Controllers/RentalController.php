<?php

namespace App\Domains\Properties\Http\Controllers;

use Exception;
use App\Domains\Properties\Models\Property;
use App\Http\Controllers\InertiaController;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Services\PropertyUnitService;

class RentalController extends InertiaController
{
  public function __construct(PropertyUnit $unit)
  {
      $this->model = $unit;
      $this->searchable  = ['name'];
      $this->templates = [
          "index" => 'Rentals/UnitList',
          "create" => 'Rentals/PropertyForm',
          "edit" => 'Rentals/PropertyForm',
          "show" => 'Rentals/Show'
      ];
      $this->validationRules = [
        'owner_id' => 'numeric',
        'address' => 'string',
        'price' => 'required'
      ];
      $this->sorts = ['created_at'];
      $this->includes = ['property', 'owner', 'contract', 'contract.client'];
      $this->filters = [
        "status" => PropertyUnit::STATUS_AVAILABLE
      ];
      $this->page = 1;
      $this->limit = 10;
      $this->resourceName= "units";
  }

  public function addUnit(Property $property) {
    $postData = request()->only(['name', 'price', 'description', 'bedrooms', 'area', 'bathrooms']);
    PropertyService::addUnit($property,  $postData);
  }

  public function removeUnit(Property $property, PropertyUnit $propertyUnit) {
    try {
      PropertyService::removeUnit($property, $propertyUnit);
    } catch (Exception $e) {
      return redirect()->back()->withErrors($e->getMessage());
    }
  }

  public function updateUnit(Property $property, PropertyUnit $propertyUnit) {
    try {
      $postData = request()->only(['name', 'price', 'description', 'bedrooms', 'area', 'bathrooms']);
      PropertyService::updateUnit($propertyUnit, $postData);
    } catch (Exception $e) {
      return redirect()->back()->withErrors($e->getMessage());
    }
  }

  public function listBadState(PropertyUnitService $propertyUnitService) {
    try {
      return inertia('Properties/UnitList', [
        'units' => $propertyUnitService
        ->withBadStatus(auth()->user()->current_team_id)
        ->with(['property', 'owner', 'contract', 'contract.client'])->get()
      ]);
    } catch (Exception $e) {
      return redirect()->back()->withErrors($e->getMessage());
    }
  }
}
