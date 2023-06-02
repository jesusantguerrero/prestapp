<?php

namespace App\Http\Controllers;

use App\Domains\Accounting\Widget\AccountStatWidget;
use App\Domains\CRM\Models\Client;
use App\Domains\CRM\Services\ClientService;
use App\Domains\Loans\Models\LoanInstallment;
use App\Domains\Loans\Services\LoanService;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Services\OwnerService;
use App\Domains\Properties\Services\PropertyService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\Traits\HasEnrichedRequest;
use Illuminate\Http\Request;
use Insane\Journal\Helpers\ReportHelper;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Invoice\Invoice;

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
      $teamId = $request->user()->current_team_id;
      $filters = $request->query('filter');
      [$startDate, $endDate] = $this->getFilterDates($filters);
      $startYear = now()->startOfYear()->format('Y-m-d');
      $endYear = now()->endOfYear()->format('Y-m-d');

      return inertia('Dashboard/Index',
      [
          "revenue" => $reportHelper->mapInMonths($reportHelper->getTransactionsByAccount($teamId, ['real_state', 'loans', 'real_state_operative'] ,$startYear, $endYear, null)->all(), now()->format('Y')),
          "stats" => AccountStatWidget::stats($teamId, $startDate, $endDate),
          'accounts' => $reportHelper->getTransactionsByAccount($teamId, ['real_state', 'loan_business', 'loans'] ,$startDate, $endDate, 'display_id'),
          'paidCommissions' => AccountStatWidget::balanceInPeriodFor('real_state_operative', $teamId, $startDate, $endDate),
          'dailyBox' => $reportHelper->smallBoxRevenue('loan_business', $teamId),
          'realState' => Account::where(['team_id' => $teamId, 'display_id' => 'real_state'])->first(),
          'section' => "general",
          'pendingDraws' => OwnerService::pendingDrawsCount($teamId),
          "serverSearchOptions" => $this->getServerParams(),
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
}
