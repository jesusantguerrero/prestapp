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
      "ledger" => $ledger,
      "categoryAccounts" => $categoryAccounts
    ] = ReportHelper::getGeneralLedger(request()->user()->current_team_id, $reportName, [
      "account_id" => $accountId,
      "dates" => [$startDate, $endDate]
    ]);

    return inertia(config('journal.statements_inertia_path') . '/Category', [
      "categories" => $categoryAccounts,
      "ledger" => $ledger->groupBy('display_id'),
      'categoryType' => $reportName,
      'serverSearchOptions' => [
        'filters' => $filters,
        'search' => $search,
      ]
    ]);
}
}
