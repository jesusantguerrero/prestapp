<?php

namespace App\Http\Controllers\CRM;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\RentService;
use Illuminate\Http\Request;
use Exception;

trait ClientTenant
{
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
