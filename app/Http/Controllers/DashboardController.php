<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Loans\Services\LoanService;
use Illuminate\Http\Request;
use Insane\Journal\Helpers\ReportHelper;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
      $reportHelper = new ReportHelper();
      $teamId = $request->user()->current_team_id;

      return inertia('Dashboard/Index',
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
