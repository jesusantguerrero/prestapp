<?php

namespace App\Domains\Accounting\Widget;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Helpers\ReportHelper;
use Illuminate\Database\Query\JoinClause;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Loans\Models\LoanInstallment;

class AccountStatWidget {

  public static function stats(int $teamId, $startDate = null, $endDate = null) {
    $today = now()->timezone('America/Santo_Domingo')->format('Y-m-d');


    $loans = LoanInstallment::byTeam($teamId)
      ->where('amount_due', '>', 0)
      ->when($startDate, fn ($q) => $q->where('due_date', '>=', $startDate))
      ->when($endDate, fn ($q) => $q->where('due_date', '<=', $endDate))
      ->when($endDate && $startDate, fn ($q) => $q->where('due_date', '<=', $today))
      ->selectRaw('amount_due, due_date')
      ->select(DB::raw("
        sum(COALESCE(amount_due, 0)) outstanding,
        sum(COALESCE(
          CASE
          WHEN now() > due_date THEN amount_due
          ELSE 0
        END, 0)) overdue, team_id")
      )->first();



      $stats = DB::table('invoices')
      ->selectRaw('clients.names contact, clients.id contact_id, invoices.debt, invoices.due_date, invoices.id id, invoices.concept')
        ->where([
          'invoices.team_id' => $teamId,
          'invoices.type' => 'INVOICE',
        ])
        ->where(fn ($q) => $q->where('invoiceable_type', Rent::class)->orWhereNull('invoiceable_type'))
        ->where('debt', '>', 0)
        ->when($endDate, fn ($q) => $q->where('due_date', '<=', $endDate))
        ->when($endDate && $startDate, fn ($q) => $q->where('due_date', '<=', $today))
        ->selectRaw("
          COALESCE(sum(debt), 0) as outstanding,
          COALESCE(sum(
            CASE
            WHEN due_date < ? AND invoices.status = ? THEN debt
            ELSE 0
          END), 0) as overdue,
          COALESCE(sum(
            CASE
            WHEN due_date > ? AND due_date < ? AND invoices.status = ? THEN debt
            ELSE 0
          END), 0) as overdue_in_month,
          COALESCE(sum(
            CASE
            WHEN due_date >= ? AND due_date <= ? THEN debt
            ELSE 0
          END), 0) as outstanding_in_month,
          GROUP_CONCAT(CASE
          WHEN due_date < ? then concat(invoices.id, ':', invoices.due_date) else '' end)
          ",
          [$endDate ?? $today,
          $endDate ?? $today,
          Invoice::STATUS_OVERDUE,
          $startDate,
          $endDate,
          Invoice::STATUS_OVERDUE,
          $startDate,
          $endDate
        ])->join('clients', 'clients.id', '=', 'invoices.client_id')
        ->first();

        $stats = collect([$loans, $stats])->reduce(function($stats, $item) {
        $stats['outstanding'] += $item->outstanding;
        $stats['overdue']  += $item->overdue;
        return $stats;
      }, [
        "outstanding" => 0,
        "overdue" => 0
      ]);

      return $stats;
  }

  public static function accountNetByPeriod($teamId, $accountDisplayId, $timeUnit = 'month', $timeUnitDiff = 2 , $type = 'expenses') {
    $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    $startDate = Carbon::now()->startOfYear()->format('Y-m-d');

    $results = ReportHelper::getTransactionsByAccount($teamId, [$accountDisplayId], $startDate, $endDate, "account_display_id");
    $results =  collect($results[$accountDisplayId] ?? [[]]);

    return [
      "year" => $results->sum('income'),
      "months" => (new ReportHelper())->mapInMonths($results->all(), now()->format('Y')),
      "avg" => $results->avg('income')
    ];
  }

  public static function balanceInPeriodFor($accountUniqueId, $teamId, $startDate, $endDate) {
    return DB::table('transaction_lines')
    ->where([
        'transaction_lines.team_id' => $teamId
    ])
    ->selectRaw("sum(amount * transaction_lines.type)  as total,
    sum(COALESCE(
      CASE
      WHEN date >= ? && date <= ? THEN amount * transaction_lines.type
      ELSE 0
    END, 0)) totalInPeriod
    ", [
      $startDate,
      $endDate
    ])
    ->join('accounts', function (JoinClause $join) use ($accountUniqueId) {
      $join->on('accounts.id', '=', 'account_id')
          ->where('accounts.display_id', $accountUniqueId)
          ->orWhere('accounts.id', $accountUniqueId);
    })
    ->first();
  }
}
