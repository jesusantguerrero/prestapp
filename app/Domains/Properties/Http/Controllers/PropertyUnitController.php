<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\Properties\Models\PropertyUnit;
use App\Http\Controllers\InertiaController;

class PropertyUnitController extends InertiaController
{
  public function __construct(PropertyUnit $unit)
  {
      $this->model = $unit;
      $this->searchable  = ['name'];
      $this->templates = [
          "index" => 'Properties/UnitList',
          "create" => 'Properties/PropertyForm',
          "edit" => 'Properties/PropertyForm',
          "show" => 'Properties/Show'
      ];
      $this->validationRules = [
          'owner_id' => 'numeric',
                                                                                    'address' => 'string',
          'price' => 'required'
      ];
      $this->sorts = ['created_at'];
      $this->includes = ['property', 'owner', 'contract', 'contract.client'];
      $this->filters = [];
      $this->page = 1;
      $this->limit = 10;
      $this->resourceName= "units";
  }
}
