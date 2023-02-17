<?php

namespace App\Http\Controllers\Properties;

use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Services\OwnerService;
use App\Http\Controllers\InertiaController;
use Illuminate\Http\Request;

class PropertyOwnerController extends InertiaController
{

  public function __construct(Property $property)
  {
      $this->model = $property;
      $this->searchable = ['name'];
      $this->templates = [
          "index" => 'Properties/Index',
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
      $this->includes = ['owner', 'units'];
      $this->filters = [];
      $this->resourceName= "properties";
  }

    public function __invoke(Request $request) {
      $teamId = $request->user()->current_team_id;
      $filters = $request->query('filters');
      $ownerId = $filters ? $filters['owner'] : null;

      return inertia('Properties/Transactions/OwnerDraws', [
        "invoices" => OwnerService::pendingDraws($teamId, $ownerId),
      ]);
    }
}
