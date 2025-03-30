<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\CRM\Models\Client;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Helpers\ReportHelper;
use App\Domains\Admin\Data\AppProfileEnum;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\CRM\Services\ClientService;
use App\Domains\Loans\Services\LoanService;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use App\Domains\Loans\Models\LoanInstallment;
use App\Domains\Properties\Services\RentService;
use App\Domains\Properties\Services\OwnerService;
use App\Http\Controllers\Traits\HasEnrichedRequest;
use App\Domains\Accounting\Widget\AccountStatWidget;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Services\PropertyUnitService;

class DashboardController extends Controller
{
    use HasEnrichedRequest;

    public function __invoke(Request $request, $section = "general")
    {
      return $this->$section($request);
    }

    public function general(Request $request)
    {
      $reportHelper = new ReportHelper();
      $team = $request->user()->currentTeam;
      $teamId = $team?->id;
      $filters = $request->query('filter');
      [$startDate, $endDate] = $this->getFilterDates($filters);
      $startYear = now()->startOfYear()->format('Y-m-d');
      $endYear = now()->endOfYear()->format('Y-m-d');

      $role = $team ? strtolower($request->user()->teamRole($team)?->name) : null;

      if (!$role || $role == 'property') {
        return $this->owner();
      }

      $accounts = [
        AppProfileEnum::Renting->value => [
          'revenueAccounts' => ['real_state', 'loans', 'real_state_operative'],
          'revenue' => ['real_state', 'loan_business', 'loans'],
          "dailyBox" => 'loan_business',
          'realEstate' => 'real_state',
          'commissions' => 'real_state_operative'
        ],
        AppProfileEnum::SheinStore->value => [
          'revenueAccounts' => ['cash_and_bank'],
          'dailyBox' => "daily_box",
          'realEstate' => 'daily_box',
          'revenue' => ['cash_and_bank'],
          'commissions' => 'daily_box'
        ],
        AppProfileEnum::Admin->value => [
          'revenueAccounts' => ['cash_and_bank'],
          'dailyBox' => "daily_box",
          'realEstate' => 'daily_box',
          'revenue' => ['cash_and_bank'],
          'commissions' => 'daily_box'
        ]
      ];

      $appAccounts = $accounts[request()->user()->currentTeam->app_profile_name ?? AppProfileEnum::Renting->value];
      $propertyTotals = PropertyService::totalByStatusFor($teamId);
      $rentTotals = RentService::invoiceByPaymentStatus($teamId, $startDate, $endDate);
      $monthPassedInYear = now()->diffInMonths(now()->startOfYear());
      $rentsStats = RentService::rentsStats($teamId);


      return inertia('Dashboard/Index',
      [
          "revenue" => $reportHelper->mapInMonths($reportHelper->getTransactionsByAccount($teamId,
            $appAccounts["revenueAccounts"],
            $startYear,
            $endYear,
            null)->all(), now()->format('Y')
          ),
          "stats" => AccountStatWidget::stats($teamId, $startDate, $endDate),
          'accounts' => $reportHelper->getTransactionsByAccount($teamId, $appAccounts["revenue"] ,$startDate, $endDate, 'display_id'),
          'paidCommissions' => AccountStatWidget::balanceInPeriodFor($appAccounts["commissions"], $teamId, $startDate, $endDate),
          'dailyBox' => $reportHelper->smallBoxRevenue($appAccounts["dailyBox"], $teamId),
          'realState' => Account::where(['team_id' => $teamId, 'display_id' => $appAccounts["realEstate"]])->first(),
          'section' => "general",
          'pendingDraws' => OwnerService::pendingDrawsCount($teamId),
          "serverSearchOptions" => $this->getServerParams(),
          "expiringRents" => RentService::expiredRentStats($teamId),
          "propertyStats" => $propertyTotals,
          "ownerStats" => [
            "total" => Client::where('team_id', $teamId)->owner()->active()->count(),
            "paid" => Invoice::where('team_id', $teamId)
              ->category(PropertyInvoiceTypes::OwnerDistribution->value)
              ->whereBetween('due_date', [$startDate, $endDate])
              ->paid()
              ->sum('total')
          ],
          "rentsStats" => $rentsStats,
          "totals" => $rentTotals,
          'pendingDraws' => OwnerService::pendingDrawsCount($teamId) ?? 0,
          "paidCommissions" => AccountStatWidget::accountNetByPeriod($teamId, 'real_state_operative', 'month', $monthPassedInYear),
          'section' => "realState",
          "serverSearchOptions" => $this->getServerParams(),
          "unitsRequiringAction" => PropertyUnitService::getUnitsRequiringAction($teamId),
        ]);
    }

    public function property(Request $request) {
      $reportHelper = new ReportHelper();
      $teamId = $request->user()->current_team_id;

      $startDate = now()->startOfYear()->format('Y-m-d');
      $endDate = now()->endOfYear()->format('Y-m-d');
      $monthPassedInYear = now()->diffInMonths(now()->startOfYear());

      $filters = $request->query('filter');
      [$startRange, $endRange] = $this->getFilterDates($filters);


      $propertyTotals = PropertyService::totalByStatusFor($teamId);
      $rentTotals = RentService::invoiceByPaymentStatus($teamId, $startRange, $endRange);

      return inertia('Dashboard/Properties',
      [
          "revenue" => $reportHelper->mapInMonths($reportHelper->getAccountTransactionsByMonths($teamId,
            ["real_state"] ,
            $startDate,
            $endDate,
            'months',
            Payment::class)->all(),
            now()->format('Y')
          ),
          "expiringRents" => RentService::expiredRentStats($teamId),
          "stats" => [
            "total" => $propertyTotals->sum(),
            "available" => $propertyTotals->get(Property::STATUS_AVAILABLE),
            "rented" => $propertyTotals->get(Property::STATUS_RENTED),
          ],
          "ownerStats" => [
            "total" => Client::where('team_id', $teamId)->owner()->active()->count(),
            "paid" => Invoice::where('team_id', $teamId)
              ->category(PropertyInvoiceTypes::OwnerDistribution->value)
              ->whereBetween('due_date', [$startRange, $endRange])
              ->paid()
              ->sum('total')
          ],
          "totals" => $rentTotals,
          'pendingDraws' => OwnerService::pendingDrawsCount($teamId) ?? 0,
          "paidCommissions" => AccountStatWidget::accountNetByPeriod($teamId, 'real_state_operative', 'month', $monthPassedInYear),
          'section' => "realState",
          "serverSearchOptions" => $this->getServerParams(),
      ]);
    }

    public function loan(Request $request) {
      $reportHelper = new ReportHelper();
      $teamId = $request->user()->current_team_id;

      return inertia('Dashboard/Loans',
      [
          "revenue" => $reportHelper->revenueReport($teamId, 'payments', LoanInstallment::class),
          "activeLoanClients" => ClientService::clientsWithActiveLoans($teamId),
          "loanCapital" => LoanService::disposedCapitalFor($teamId),
          "loanExpectedInterest" => LoanService::expectedInterestFor($teamId),
          "loanPaidInterest" => LoanService::paidInterestFor($teamId),
          "paidInterest" => LoanService::paidInterestByPeriod($teamId),
          'bank' => $reportHelper->smallBoxRevenue('loan_business', $teamId),
          'section' => "loans",
          "serverSearchOptions" => $this->getServerParams(),
      ]);
    }

    public function owner() {
      $reportHelper = new ReportHelper();
      $teamId = request()->user()->current_team_id;

      return inertia('Dashboard/Owner',
      [

      ]);
    }
}
