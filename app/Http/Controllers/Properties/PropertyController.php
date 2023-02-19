<?php

namespace App\Http\Controllers\Properties;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\InertiaController;
use Illuminate\Http\Request;

class PropertyController extends InertiaController
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

    public function create(Request $request) {
      $teamId = $request->user()->current_team_id;

      return inertia($this->templates['create'], [
            'properties' => null,
            'clients' => ClientService::ofTeam($teamId),
        ]);
    }

    protected function createResource(Request $request, $postData)
    {
        return PropertyService::createProperty($postData, $request->get('units'));
    }

    public function getEditProps(Request $request, $resource) {
      $teamId = $request->user()->current_team_id;

      return [
        'clients' => ClientService::ofTeam($teamId),
      ];
    }

    // Units
    public function addUnit(Property $property) {
      $postData = request()->only(['name', 'price', 'description']);
      PropertyService::addUnit($property,  $postData);
    }

    // Tools

    public function managementTools(Request $request) {
      $teamId = $request->user()->current_team_id;
      $tab = $request->query('tab', 'rents');
      $filters = $request->query('filters');
      $ownerId = $filters ? $filters['owner'] : null;

      $invoices = $tab == 'fees'
      ? RentService::invoices($teamId)->get()
      : ClientService::invoices($teamId, $ownerId)->get();

      return inertia('Properties/Transactions/ManagementTools',
      [
          'invoices' => $invoices,
          'outstanding' => $invoices->sum('debt'),
          'paid' => $invoices->sum(function ($invoice) {
            return $invoice->total - $invoice->debt;
          }),
          'owners' => $invoices->map(function($invoice) {
            return [
              "value" => $invoice->client_id,
              "label" => $invoice->client_name,
            ];
          })->unique('value')->values(),
          'properties' => $invoices->map(function($invoice) {
              return [
                "value" => $invoice->client_id,
                "label" => $invoice->client_name,
              ];
          })
      ]);
    }
}
