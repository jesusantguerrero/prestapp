<?php

namespace App\Http\Controllers\CRM;

use Exception;
use Illuminate\Http\Request;
use App\Domains\CRM\Models\Client;
use App\Domains\CRM\Data\ContactData;
use App\Domains\CRM\Services\ClientService;
use App\Http\Controllers\InertiaController;

class ClientController extends InertiaController
{
  use ClientTabs;

  public function __construct(Client $client, protected ClientService $clientService)
  {
      $this->model = $client;
      $this->searchable = ['names', 'lastnames'];
      $this->templates = [
          "index" => 'Clients/Index',
          "show" => 'Clients/Show'
      ];
      $this->sorts = ['created_at'];
      $this->includes = ['properties', 'account'];
      $this->filters = [];
      $this->page = 1;
      $this->limit = 10;
      $this->resourceName= "clients";
      $this->validationRules = [
        'names' => 'required',
        'lastnames' => 'required',
        'dni' => 'required',
      ];
  }

  public function createResource(Request $request, $postData) {
    try {
      $resource = $this->clientService->create($postData);
      return $resource;
    } catch (Exception $e) {
      $message = $e->getMessage();
      return response()->json([
        'message' => $message
      ], 400);
    }
  }

  protected function byTypes(Request $request, $type) {
    $resourceName = $this->resourceName ?? $this->model->getTable();
    $resources = $this->parser($this->getModelQuery($request,null, function ($builder) use ($type) {
      $builder->where("is_$type", true);
    }));

    return inertia($this->templates['index'],
    array_merge([
        $resourceName => $resources,
        "serverSearchOptions" => $this->getServerParams(),
        "type" => $type,
    ], $this->getIndexProps($request, $resources)));
  }

  public function show(Request $request, int $id) {
    $client = Client::where('id', $id)->first();

    return inertia($this->templates['show'],
      array_merge(
          $this->getEditProps($request, $id), [
          $this->model->getTable() => $client,
          "is_owner" => $client->is_owner,
          "outstanding" => $client->outstandingBalance(),
          "deposits" => $client->deposits(),
          "credits" => $client->credits
        ])
    );
  }

  public function showByType(Client $client, $type) {
    $section = request()->query('section');

    $resource = array_merge($client->toArray(),[
      ...($section ? $this->$section($client) : [] ),
    ], [
      "property_count" => $client->properties()->count(),
      "unit_count" => $client->units()->count()
    ]);

    $templates = [
      "tenant" => "Clients/TenantDetail",
      "owner" => "Clients/OwnerDetail",
      "lender" => $this->templates['show']
    ];
    $template = $templates[$type] ??  $this->templates['show'];

    return inertia($template,[
      $this->model->getTable() => $resource,
      "outstanding" => $client->outstandingBalance(),
      "deposits" => $client->deposits(),
      "credits" => $client->credits,
      "contract" => function() use ($client, $type) {
        return $type == 'tenant' ? $client->rent : null;
      },
      "stats" => function() use ($type, $client) {
        if ($type == 'tenant') {
          return [
            "balance" => $client->depositBalance()
          ];
        }
      },
      "leases" => function() use ($client, $type) {
        return $type == 'owner' ? $client->leases : null;
      },
      "type" => $type,
      "currentTab" => $section
    ]);
  }

  public function validateDelete(Request $request, $client) {
    if ($client->hasActiveLoans()) {
      throw new Exception(__("This client has active loans"));
    }

    if ($client->rents()->count()) {
      throw new Exception(__("This client has current rents"));
    }

    if ($client->properties()->count()) {
      throw new Exception(__("This client has current properties"));
    }

    return true;
  }

  protected function getValidationRules($postData) {
    return [
      'names' => 'required',
      'lastnames' => 'required',
      'dni' => 'required',
    ];
  }
}
