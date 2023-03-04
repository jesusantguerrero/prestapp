<?php

namespace App\Http\Controllers\CRM;

use App\Domains\CRM\Models\Client;
use App\Http\Controllers\InertiaController;
use Illuminate\Http\Request;

class ClientController extends InertiaController
{
  use ClientTabs;
  use ClientTenant;

  public function __construct(Client $client)
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
        'address_details' => 'required',
        'dni' => 'required',
        'email' => 'required',
        'cellphone' => 'required',
        'work_name' => 'required',
      ];
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

    return inertia($this->templates['show'],[
      $this->model->getTable() => $resource,
      "outstanding" => $client->outstandingBalance(),
      "deposits" => $client->deposits(),
      "credits" => $client->credits,
      "type" => $type,
      "currentTab" => $section
    ]);
  }

}
