<?php

namespace App\Http\Controllers\CRM;

use App\Domains\CRM\Models\Client;

trait ClientTabs
{
  public function getSection(Client $client, string $section) {
    $resourceName = $this->resourceName ?? $this->model->getTable();
    $resource = $client->toArray();

    return inertia($this->templates['show'],
    [
        $resourceName => array_merge($resource, $this->$section($client)),
        "currentTab" => $section
    ]);
  }

  //  tenants
  public function contracts(Client $client) {
    return [
      "leases" => $client->rents,
    ];
  }

  public function transactions(Client $client) {

    return [
        "invoices" => $client->invoices
    ];
  }

   //  Owners
   public function properties(Client $client) {
    return [
      "properties" => $client->properties,
    ];
  }


  // Lenders

  public function loans(Client $client) {
    return [
      "loans" => $client->loans,
    ];
  }
}
