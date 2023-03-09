<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Services\OwnerDistributionService;
use App\Domains\Properties\Services\OwnerService;
use App\Domains\Properties\Services\PropertyTransactionService;
use App\Http\Controllers\InertiaController;
use Exception;
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

      $invoicesByClient = OwnerService::pendingDraws($teamId, $ownerId);

      return inertia('Properties/Transactions/OwnerDraws', [
        "invoices" => $invoicesByClient,
        'outstanding' => 0,
        'outstanding' => $invoicesByClient->sum(function ($client) {
          return $client['invoices']->sum('total');
        }),
      ]);
    }

    public function generateDraw(Client $client, int $invoiceId = null) {
      PropertyTransactionService::createOwnerDistribution($client, $invoiceId);
      return redirect("/properties/management-tools");
    }

    public function storeDraws(Client $client) {
      $postData = request()->post();
      $ownerDistribution = new OwnerDistributionService($client);
      $ownerDistribution->fromRequest($postData);
      return redirect("/properties/management-tools");
    }

    public function updateDraws(Client $client, int $drawId) {
      $postData = request()->post();
      $ownerDistribution = new OwnerDistributionService($client);
      $ownerDistribution->updateFromRequest($postData, $drawId);
      return redirect("/properties/management-tools");
    }

    public function payDraw(Client $client, int $drawId) {
      try {
        $postData = request()->post();
        $ownerDistribution = new OwnerDistributionService($client);
        $ownerDistribution->recordPayment($drawId, $postData);
        return back();
      } catch (Exception $e) {
        return back()->withErrors(["error"  => $e->getMEssage()]);
      }
    }
}
