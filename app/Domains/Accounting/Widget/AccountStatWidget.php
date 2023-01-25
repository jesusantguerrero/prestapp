<?php

namespace App\Domains\Accounting\Widget;

use App\Domains\Loans\Models\LoanInstallment;
use Illuminate\Support\Facades\DB;

class AccountStatWidget {

  public static function stats(int $teamId) {
    $loans = LoanInstallment::byTeam($teamId)
      ->where('amount_due', '>', 0)
      ->selectRaw('amount_due, due_date')
      ->select(DB::raw("
        sum(COALESCE(amount_due, 0)) outstanding,
        sum(COALESCE(
          CASE
          WHEN now() > due_date THEN amount_due
          ELSE 0
        END, 0)) overdue, team_id")
      )->first();

      $stats = DB::table('invoices')->selectRaw('clients.names contact, clients.id contact_id, invoices.debt, invoices.due_date, invoices.id id, invoices.concept')
        ->where([
          'invoices.team_id' => $teamId,
          'invoices.type' => 'INVOICE',
          'invoiceable_type' => Rent::class
        ])
        ->where('debt', '>', 0)
        ->select(DB::raw("
            COALESCE(sum(debt), 0) as outstanding,
            COALESCE(sum(
            CASE
            WHEN now() > due_date THEN debt
            ELSE 0
          END), 0) as overdue")
        )
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
}
