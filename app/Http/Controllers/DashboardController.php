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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Helpers\ReportHelper;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(Request $request, $section = "general")
    {
      return $this->$section($request);
    }

    public function general(Request $request)
    {
      $reportHelper = new ReportHelper();
      $teamId = $request->user()->current_team_id;

      return inertia('Dashboard/Index',
      [
          "revenue" => $reportHelper->revenueReport($teamId),
          "stats" => AccountStatWidget::stats($teamId),
          'accounts' => $reportHelper->getChartTransactionsByPeriod($teamId, ['assets', 'liabilities']),
          'bank' => $reportHelper->smallBoxRevenue('bank', $teamId),
          'dailyBox' => $reportHelper->smallBoxRevenue('daily_box', $teamId),
          'realState' => Account::where(['team_id' => $teamId, 'display_id' => 'real_state'])->first(),
          'logs' => Activity::all()->last(),
          'section' => "general",
          'pendingDraws' => OwnerService::pendingDrawsCount($teamId),
      ]);
    }

    public function property(Request $request) {
      $reportHelper = new ReportHelper();
      $teamId = $request->user()->current_team_id;

      $propertyTotals = PropertyService::totalByStatusFor($teamId);

      $invoices = RentService::invoices($teamId);
      $qInvoices = RentService::invoices($teamId);

      return inertia('Dashboard/Properties',
      [
          "revenue" => $reportHelper->revenueReport($teamId),
          "stats" => [
            "total" => $propertyTotals->sum(),
            "available" => $propertyTotals->get(Property::STATUS_AVAILABLE),
            "rented" => $propertyTotals->get(Property::STATUS_RENTED),
          ],
          "ownerStats" => [
            "total" => Client::where('team_id', $teamId)->owner()->active()->count(),
            "paid" => Invoice::where('team_id', $teamId)
              ->category(PropertyInvoiceTypes::OwnerDistribution->value)
              ->paid()
              ->sum('total')
          ],
          "totals" => $invoices->select(DB::raw("sum(total) total, sum(total-debt) paid, sum(debt) outstanding, sum(
            CASE
            WHEN invoices.debt > 0 THEN 1
            ELSE 0
          END) outstandingInvoices"))->first(),
          'accounts' => $reportHelper->getAccountTransactionsByPeriod($teamId, ['rent', 'security_deposits', 'operating_expense']),
          'nextInvoices' => $qInvoices->unpaid()->take(4)->get(),
          "paidCommissions" => AccountStatWidget::accountNetByPeriod($teamId, 'real_state_operative'),
          'section' => "realState"
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
          'section' => "loans"
      ]);
    }
}
