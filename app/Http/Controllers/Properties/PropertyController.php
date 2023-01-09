<?php

namespace App\Http\Controllers\Properties;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Loans\Services\LoanService;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\InertiaController;
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
          'price' => 'required'
      ];
      $this->sorts = ['created_at'];
      $this->includes = ['owner', 'units'];
      $this->filters = [];
      $this->resourceName= "properties";
  }

    public function __invoke(Request $request) {
      $reportHelper = new ReportHelper();
      $teamId = $request->user()->current_team_id;
      $tab = $request->query('tab', 'rents');

      $propertyTotals = PropertyService::totalByStatusFor($teamId);

      $invoices = $tab == 'rents'
      ? RentService::invoices($teamId)->get()
      : ClientService::invoices($teamId)->get();

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
          'nextInvoices' => $invoices,
      ]);
    }

    public function create(Request $request) {
      $teamId = $request->user()->current_team_id;

      return inertia($this->templates['create'], [
            'properties' => null,
            'clients' => ClientService::ofTeam($teamId),
        ]);
    }

    protected function createResource(Request $request, $postData)
    {
        return PropertyService::createProperty($postData, $request->get('units'));
    }


    public function getEditProps(Request $request, $id) {
      $teamId = $request->user()->current_team_id;

      return [
        'clients' => ClientService::ofTeam($teamId),
      ];
    }

    public function managementTools(Request $request) {
      $teamId = $request->user()->current_team_id;
      $tab = $request->query('tab', 'rents');
      $filters = $request->query('filters');
      $ownerId = $filters ? $filters['owner'] : null;

      $invoices = $tab == 'fees'
      ? RentService::invoices($teamId)->get()
      : ClientService::invoices($teamId, $ownerId)->get();

      return inertia('Properties/ManagementTools',
      [
          'invoices' => $invoices,
          'outstanding' => $invoices->sum('debt'),
          'paid' => $invoices->sum(function ($invoice) {
            return $invoice->total - $invoice->debt;
          }),
          'owners' => $invoices->map(function($invoice) {
            return [
              "value" => $invoice->client_id,
              "label" => $invoice->client_name,
            ];
          })->unique('value')->values(),
          'properties' => $invoices->map(function($invoice) {
              return [
                "value" => $invoice->client_id,
                "label" => $invoice->client_name,
              ];
          })
      ]);
    }
}
