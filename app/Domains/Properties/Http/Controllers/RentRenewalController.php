<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\InertiaController;
use Illuminate\Http\Request;

class RentRenewalController extends InertiaController
{
    public function __construct(Rent $rent, RentService $rentService)
    {
        $this->model = $rent;
        $this->searchable = ['client_name', 'owner_name', 'address'];
        $this->templates = [
            "index" => 'Rents/RenewalList',
        ];
        $this->validationRules = [
            'client_id' => 'numeric',
            'amount' => 'numeric',
            'count' => 'numeric',
            'frequency' => 'string',
            'grace_days' => 'numeric',
            'commission' => 'numeric',
            'installments' => 'array'
        ];
        $this->sorts = ['end_date'];
        $this->includes = ['client', 'property', 'unit'];
        $this->filters = [];
        $this->page = 1;
        $this->limit = 10;
        $this->service = $rentService;
    }

    protected function index(Request $request) {
      $resourceName = $this->resourceName ?? $this->model->getTable();
      $teamId = $request->user()->current_team_id;
      $resources = $this->parser($this->getModelQuery($request, null, fn ($q) => $q->whereNotNull('end_date')
      ->whereRaw('DATEDIFF(end_date, now()) <= 90')
      ->whereNotIn('status', [Rent::STATUS_CANCELLED])));

      return inertia($this->templates['index'],
      array_merge([
          $resourceName => $resources,
          "serverSearchOptions" => $this->getServerParams(),
          "kpis" => RentService::expiredRentStats($teamId)
      ], $this->getIndexProps($request, $resources)));
  }
}
