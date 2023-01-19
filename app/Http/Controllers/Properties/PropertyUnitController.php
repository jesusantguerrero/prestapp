<?php

namespace App\Http\Controllers\Properties;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\InertiaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Helpers\ReportHelper;

class PropertyUnitController extends InertiaController
{
  public function __construct(PropertyUnit $unit)
  {
      $this->model = $unit;
      $this->searchable = ['name'];
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
      $this->resourceName= "units";
  }
}
