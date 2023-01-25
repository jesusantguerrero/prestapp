<?php

namespace App\Http\Controllers;

use App\Domains\Accounting\Widget\AccountStatWidget;
use Illuminate\Http\Request;
use Insane\Journal\Helpers\ReportHelper;
use Insane\Journal\Models\Core\Account;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
      $reportHelper = new ReportHelper();
      $teamId = $request->user()->current_team_id;

      return inertia('Dashboard/Index',
      [
          "revenue" => $reportHelper->revenueReport($teamId),
          "stats" => AccountStatWidget::stats($teamId),
          'accounts' => $reportHelper->getChartTransactionsByPeriod($teamId, ['income', 'expenses']),
          'bank' => $reportHelper->smallBoxRevenue('bank', $teamId),
          'dailyBox' => $reportHelper->smallBoxRevenue('daily_box', $teamId),
          'realState' => Account::where(['team_id' => $teamId, 'display_id' => 'real_state'])->first(),
          'logs' => Activity::all()->last()
      ]);
    }
}
