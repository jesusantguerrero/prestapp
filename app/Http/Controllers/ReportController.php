<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasEnrichedRequest;
use Illuminate\Http\Request;
use Insane\Journal\Helpers\ReportHelper;

class ReportController extends Controller
{
  use HasEnrichedRequest;

  public function statements(Request $request, string $reportName = "income") {
    $filters = $request->query('filter');
    $search = $request->query('search');
    $accountId = $filters['account'] ?? null;
    [$startDate, $endDate] = $this->getFilterDates($filters);

    [
      "ledger" => $categoryAccounts
    ] = ReportHelper::getGeneralLedger(request()->user()->current_team_id, $reportName, [
      "account_id" => $accountId,
      "dates" => [$startDate, $endDate]
    ]);

    return inertia(config('journal.statements_inertia_path') . '/Category', [
      "categories" => $categoryAccounts,
      'categoryType' => $reportName,
      'serverSearchOptions' => [
        'filters' => $filters,
        'search' => $search,
      ]
    ]);
  }

  public function payments() {
      $reportHelper = new ReportHelper();
      $teamId = request()->user()->current_team_id;

      $filters = request()->query('filter');
      [$startRange, $endRange] = $this->getFilterDates($filters);


      $categories = $reportHelper->getAccountTransactionsByMonths($teamId, ['real_state', 'expected_payments_owners'] ,$startRange, $endRange, 'display_id', Payment::class);
      return inertia(config('journal.statements_inertia_path') . '/Category', [
        "categories" => $categories,
        'categoryType' => __("Payments in month"),
        'serverSearchOptions' => [
          'filters' => $filters,
          'search' => "",
        ]
      ]);
  }
}
