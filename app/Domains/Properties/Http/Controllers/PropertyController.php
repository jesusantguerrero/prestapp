<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\InertiaController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function store(Request $request, Response $response) {
      $postData = $this->getPostData();

      $this->validate($request, $this->getValidationRules($postData));
      try {
        $resource = $this->createResource($request, $postData);
        $this->afterSave($postData, $resource);
        return to_route('properties.show', ["property" => $resource]);
      } catch (Exception $e) {
          return redirect()->back()->withErrors($e->getMessage());
      }
  }

    public function update(Request $request, int $id) {
        $resource = $this->model::findOrFail($id);
        $postData = $request->post();
        try {
        $resource = $this->updateResource($resource, $postData);
        $this->afterSave($postData, $resource);

        return to_route('properties.show', ["property" => $resource]);
      } catch (Exception $e) {
          return redirect()->back()->withErrors($e->getMessage());
      }
    }

    protected function createResource(Request $request, $postData)
    {
        return PropertyService::createProperty($postData, $request->get('units'));
    }

    public function validateDelete(Request $request, $property) {
      if ($property->rents()->count()) {
        throw new Exception(__("This property has contracts and can't be eliminated"));
      }

      return true;
    }

    public function getEditProps(Request $request, $resource) {
      $teamId = $request->user()->current_team_id;

      return [
        'clients' => ClientService::ofTeam($teamId),
      ];
    }

    // Tools
    public function managementTools(Request $request) {
      $teamId = $request->user()->current_team_id;
      $filters = $request->query('filters');
      $ownerId = $filters['owner'] ?? null;
      $section = $filters['section'] ?? 'bills';
      $status = $filters['status'] ?? null;
      $statuses = $status ? [$status] : [];

      $methods = [
        "bills" => fn() => ClientService::invoices($teamId, $ownerId)->get(),
        "invoices" => fn() => RentService::invoices($teamId, $statuses)
          ->orderByDesc('due_date')
          ->where('due_date', '<=', now()->timezone('America/Santo_Domingo'))
          ->get(),
        "commissions" => fn() => RentService::commissions($teamId)->get()
      ];

      $method = $methods[$section];
      $invoices = $method();

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
          }),
          "section" => $section
      ]);
    }
}
