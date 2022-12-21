<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Loans\Services\LoanService;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\PropertyService;
use Illuminate\Http\Request;
use Insane\Journal\Helpers\ReportHelper;

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
      ];
      $this->sorts = ['created_at'];
      $this->includes = ['owner', 'activeContract'];
      $this->filters = [];
      $this->resourceName= "properties";
  }

    public function __invoke(Request $request) {
            $reportHelper = new ReportHelper();
            $teamId = $request->user()->current_team_id;

            $propertyTotals = PropertyService::totalByStatusFor($teamId);


            return inertia('Properties/Overview',
            [
                "revenue" => $reportHelper->revenueReport($teamId),
                "propertiesTotal" => $propertyTotals->sum(),
                "propertiesAvailable" => $propertyTotals->get(Property::STATUS_AVAILABLE),
                "propertiesRented" => $propertyTotals->get(Property::STATUS_RENTED),
                "activeLoanClients" => ClientService::clientsWithActiveLoans($teamId),
                "loanCapital" => LoanService::disposedCapitalFor($teamId),
                "loanExpectedInterest" => LoanService::expectedInterestFor($teamId),
                "loanPaidInterest" => LoanService::paidInterestFor($teamId),
                'bank' => $reportHelper->smallBoxRevenue('bank', $teamId),
                'dailyBox' => $reportHelper->smallBoxRevenue('daily_box', $teamId),
                'cashOnHand' => $reportHelper->smallBoxRevenue('cash_on_hand', $teamId),
                'nextInvoices' => $reportHelper->nextInvoices($teamId),
                'debtors' => $reportHelper->debtors($teamId),
            ]);
    }

    public function create(Request $request) {
      $teamId = $request->user()->current_team_id;

      return inertia($this->templates['create'], [
            'properties' => null,
            'clients' => ClientService::ofTeam($teamId),
        ]);
    }

    public function getEditProps(Request $request, $id) {
      $teamId = $request->user()->current_team_id;

      return [
        'clients' => ClientService::ofTeam($teamId),
      ];
    }
}
