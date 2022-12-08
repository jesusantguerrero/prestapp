<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Loans\Services\LoanService;
use App\Domains\Properties\Models\Rent;
use Illuminate\Http\Request;
use Insane\Journal\Helpers\ReportHelper;

class PropertyController extends InertiaController
{
  public function __construct(Rent $rent)
  {
      $this->model = $rent;
      $this->searchable = ['name'];
      $this->templates = [
          "index" => 'Properties/Index',
          "create" => 'Rents/LoanForm',
          "show" => 'Rents/Show'
      ];
      $this->validationRules = [
          'client_id' => 'numeric',
          'amount' => 'numeric',
          'count' => 'numeric',
          'frequency' => 'string',
          'grace_days' => 'numeric',
          'interest_rate' => 'numeric|max:100',
          'installments' => 'array'
      ];
      $this->sorts = ['created_at'];
      $this->includes = ['client'];
      $this->filters = [];
      $this->resourceName= "properties";
  }

  public function __invoke(Request $request)
    {
            $reportHelper = new ReportHelper();
            $teamId = $request->user()->current_team_id;

            return inertia('Properties/Index',
            [
                "revenue" => $reportHelper->revenueReport($teamId),
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
}
