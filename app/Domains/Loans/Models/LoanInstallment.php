<?php

namespace App\Domains\Loans\Models;

use Illuminate\Database\Eloquent\Model;
use Insane\Journal\Models\Core\Transaction;
use Insane\Journal\Traits\IPayableDocument;
use Insane\Journal\Traits\HasPayments;

class LoanInstallment extends Model implements IPayableDocument {
    use HasPayments;

    const STATUS_PENDING = 'PENDING';
    const STATUS_LATE =  'LATE';
    const STATUS_PAID = 'PAID';
    const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    const STATUS_GRACE = 'GRACE';

    protected $fillable = [
        'team_id',
        'user_id',
        'loan_id',
        'installment_number',
        'due_date',
        'days',
        'amount',
        'principal',
        'interest',
        'initial_balance',
        'final_balance'
    ];

    /**
     * Scope a query to only include popular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLate($query)
    {
        return $query->where('payment_status', self::STATUS_LATE);
    }

    public function loan() {
        return $this->belongsTo(Loan::class);
    }

    public function getStatusField(): string {
        return 'payment_status';
    }

    public static function calculateTotal($payable) {
      $result = [];

      if ($payable) {
          $totalPaid = $payable->payments()->sum('amount');
          $paymentSchema = ['fees', 'late_fee', 'interest', 'principal'];
          $balance = $totalPaid;

          foreach ($paymentSchema as $fee) {
            if ($payable->$fee > 0) {
              $amountToPay = $balance >= $payable->$fee ? $payable->$fee : $balance;
              $result[$fee."_paid"] = $amountToPay;
              $balance -= $amountToPay;
            } else {
              $result[$fee."_paid"] = 0;
            }
          }

          $result['amount_paid'] = $totalPaid;
          $result['amount_due'] = $payable->amount - $totalPaid;
          LoanInstallment::where(['id' => $payable->id])
          ->update($result);
      }
    }

    public static function checkStatus($payable) {
        $today = date('Y-m-d');
        $debt = $payable->amount - $payable->amount_paid;
        if ($debt == 0) {
            $status = self::STATUS_PAID;
        } elseif ($debt > 0 && $debt < $payable->amount) {
            $status = self::STATUS_PARTIALLY_PAID;
        } elseif ($debt && $payable->due_date < $today) {
            $status = self::STATUS_LATE;
        } elseif ($debt) {
            $status = self::STATUS_PENDING;
        } else {
            $status = $payable->payment_status;
        }

        return $status;
    }

    public function getConceptLine(): string {
        return "";
    }


    // payable implementation
    public function getCounterAccountId(): int {
      return $this->loan->client_account_id;
    }

    public function getTransactionDirection(): string {
      return Transaction::DIRECTION_DEBIT;
    }
}
