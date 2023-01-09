<?php

namespace App\Http\Controllers\CRM;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Actions\GenerateInvoices;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\InertiaController;
use Illuminate\Http\Request;
use Exception;

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
      $this->sorts = ['created_at'];
      $this->includes = ['properties', 'account'];
      $this->filters = [];
      $this->resourceName= "clients";
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


  public function generatePayment(Client $client) {
    GenerateInvoices::ownerDistribution($client);
    return redirect("/bills/");
  }

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

  public function getSection(Client $client, string $section) {
    $resourceName = $this->resourceName ?? $this->model->getTable();
    $resource = $client->toArray();

    return inertia($this->templates['show'],
    [
        $resourceName => array_merge($resource, $this->$section($client)),
        "currentTab" => $section
    ]);
  }

  // Tenant
  public function endRent(Client $client, Rent $rent) {
    $resourceName = $this->resourceName ?? $this->model->getTable();
    $resource = $client->toArray();

    return inertia('Clients/EndRent',
    [
        $resourceName => $resource,
        "rent" => $rent,
        "property" => $rent->property,
        "currentTab" => 'contracts',
        "pendingInvoices" => $client->invoices()->unpaid()->get(),
        "depositsToReturn" => $client->invoices()->paid()->noRefunded()->invoiceAccount($rent->property->deposit_account_id)->get()
        // I should get the balance I have in liabilities of the deposit account instead
    ]);
  }

  public function endRentAction(Client $client, Rent $rent, Request $request) {
    try {
      RentService::endTerm($rent, $request->only(['move_out_at', 'move_out_notice']));
      return redirect("/clients/$client->id/contracts");
    } catch (Exception $e) {
      back()->withErrors(["error" => "The rent is already cancelled"]);
    }
  }
}
