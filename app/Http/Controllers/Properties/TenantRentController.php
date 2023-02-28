<?php

namespace App\Http\Controllers\Properties;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class TenantRentController extends Controller
{
  // Tenant
  public function endRent(Client $client, Rent $rent) {
    $resource = $client->toArray();

    return inertia('Clients/RentEnd',
    [
        'clients' => $resource,
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
      back()->withErrors(["error" => $e->getMessage()]);
    }
  }

  public function renewRent(Client $client, Rent $rent) {
    $resource = $client->toArray();

    return inertia('Clients/Properties/RentRenew',
    [
        'clients' => $resource,
        "rent" => $rent,
        "property" => $rent->property,
        "currentTab" => 'contracts',
        "pendingInvoices" => $client->invoices()->unpaid()->get(),
        "depositsToReturn" => $client->invoices()->paid()->noRefunded()->invoiceAccount($rent->property->deposit_account_id)->get()
        // I should get the balance I have in liabilities of the deposit account instead
    ]);
  }

  public function renewRentAction(Client $client, Rent $rent, Request $request) {
    try {
      RentService::extend($rent, $request->only(['end_date', 'amount']));
      return redirect("/clients/$client->id/contracts");
    } catch (Exception $e) {
      back()->withErrors(["error" => $e->getMessage()]);
    }
  }
}
