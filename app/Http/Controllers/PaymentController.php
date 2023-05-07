<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasEnrichedRequest;
use Illuminate\Http\Request;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Core\Transaction;

class PaymentController
{
    use HasEnrichedRequest;

    public function __invoke(Request $request)
    {
      $filters = $request->query('filter');

      [$startDate, $endDate] = $this->getFilterDates($filters);
      $direction = $filters["direction"] ?? null;
      $account = $filters["account"] ?? null;

      $payments = Payment::where([
        'team_id' => $request->user()->currentTeam->id
      ])
      // ->byClient($clientId)
      ->with(['payable', 'payable.client', 'transaction', 'account'])
      ->orderByDesc('payment_date')
      ->whereBetween('payment_date', [$startDate, $endDate])
      ->when($direction, fn ($q) => $q->whereHas('transaction', function ($query) use ($direction) {
        $query->where('direction', $direction == 'credit' ? Transaction::DIRECTION_CREDIT : Transaction::DIRECTION_DEBIT);
      }))
      ->when($account, fn ($q) => $q->whereHas('account', function ($query) use ($account) {
        $query->where('display_id', $account)
        ->orWhere('id', $account);
      }))
      ->paginate(200);

        return inertia(config('journal.payments_inertia_path') . '/Index', [
            "payments" => $payments,
            "total" => $payments->sum(function ($payment) {
              $type = $payment->transaction->direction == Transaction::DIRECTION_DEBIT ? 1 : -1 ;
              return $type * $payment->amount;
            }),
            'income' => $payments->sum(function ($payment) {
              return $payment->transaction->direction == Transaction::DIRECTION_DEBIT ? $payment->amount : 0;
            }),
            'outgoing' => $payments->sum(function ($payment) {
              return $payment->transaction->direction == Transaction::DIRECTION_CREDIT ? $payment->amount : 0;
            }),
        ]);
    }
}
