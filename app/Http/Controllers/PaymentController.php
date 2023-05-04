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

      $payments = Payment::where([
        'team_id' => $request->user()->currentTeam->id
      ])
      // ->byClient($clientId)
      ->with(['payable', 'payable.client', 'transaction'])
      ->orderByDesc('payment_date')
      ->whereBetween('payment_date', [$startDate, $endDate])
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
