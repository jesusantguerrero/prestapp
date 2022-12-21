<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Models\Client;
use App\Domains\CRM\Services\ClientService;

class ClientController extends InertiaController
{
  public function __construct(Client $client)
  {
      $this->model = $client;
      $this->searchable = ['name'];
      $this->templates = [
          "index" => 'Clients/Index',
          "show" => 'Clients/Show'
      ];
      // $this->validationRules = [
      //     'owner_id' => 'numeric',
      //     'address' => 'string',
      //     'price' => 'required'
      // ];
      $this->sorts = ['created_at'];
      $this->includes = ['properties', 'account'];
      $this->filters = [];
      $this->resourceName= "clients";
  }


  public function generatePayment(Client $client) {
    ClientService::generateBill($client);
    return redirect("/bills/");
  }

}
