<?php

namespace App\Domains\Loans\Models;

use App\Domains\CRM\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Core\Payment;
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

    protected $appends = ['clientName'];

    protected $fillable = [
        'team_id',
        'user_id',
        'loan_id',
        'client_id',
        'number',
        'due_date',
        'days',
        'amount',
        'principal',
        'late_fee',
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
        return $query->where('loan_installments.payment_status', self::STATUS_LATE);
    }
    public function scopeUnpaid($query)
    {
        return $query->whereNotIn('loan_installments.payment_status', [self::STATUS_PAID, self::STATUS_PARTIALLY_PAID]);
    }
    public function scopeByTeam($query, $teamId)
    {
        return $query->where('loan_installments.team_id', $teamId);
    }
    public function scopeByClient($query, $clientId)
    {
        return $query->where('loan_installments.client_id', $clientId);
    }

    public function loan() {
        return $this->belongsTo(Loan::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function getClientNameAttribute() {
      return $this->client?->display_name;
    }

    public function getStatusField(): string {
        return 'payment_status';
    }

    public static function calculateTotal($payable) {
      if ($payable) {
          $totalPaid = $payable->payments()->sum('amount');
          $paymentSchema = ['fees', 'late_fee', 'interest', 'principal'];
          $balance = $totalPaid;

          foreach ($paymentSchema as $fee) {
            $feePaidField = $fee."_paid";
            if ($payable->$fee > 0) {
              $amountToPay = $balance >= $payable->$fee ? $payable->$fee : $balance;
              $payable->$feePaidField = $amountToPay;
              $balance -= $amountToPay;
            } else {
              $payable->$feePaidField = 0;
            }
          }

          $payable->amount_paid = $totalPaid;
          $payable->amount_due = $payable->amount - $totalPaid;
      }
    }

    public function updateStatus() {
      LoanInstallment::calculateTotal($this);
      LoanInstallment::checkStatus($this);
    }

    public static function checkStatus($payable) {
        $today = date('Y-m-d');
        $debt = $payable->amount - $payable->amount_paid;
        if ($debt <= 0) {
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

    public function createPaymentTransaction(Payment $payment) {
      $direction = $this->getTransactionDirection() ?? Transaction::DIRECTION_DEBIT;
      $counterAccountId = $this->getCounterAccountId();

      return [
        "team_id" => $payment->team_id,
        "user_id" => $payment->user_id,
        "date" => $payment->payment_date,
        "description" => $payment->concept ?? "Pago cuota #" . $this->number,
        "concept" => $payment->concept ?? "Pago cuota #" . $this->number,
        "direction" => $direction,
        "total" => $payment->amount,
        "account_id" => $payment->account_id,
        "counter_account_id" => $counterAccountId,
        "items" => $this->getTransactionItems($payment, $counterAccountId)
      ];
    }

    protected function getTransactionItems($payment)
    {
      $paymentSchema = [
        'fees' => [
          "total" => 0,
          "account_id" => $this->loan->fees_account_id
        ],
        'late_fee' => [
          "total" => 0,
          "account_id" => $this->loan->late_fee_account_id
        ],
        'interest' => [
          "total" => 0,
          "account_id" => $this->loan->fees_account_id
        ],
        'principal' => [
          "total" => 0,
          "account_id" => $this->loan->client_account_id
        ]
      ];

      $totalPaid = $payment->amount;
      $balance = $totalPaid;
      $items = [];
      $count = 1;

      $items[] = [
        "index" => 0,
        "account_id" => $payment->account_id,
        "category_id" => null,
        "type" => 1,
        "concept" => $payment->concept ?? "Pago cuota #" . $this->number,
        "amount" => $totalPaid,
        "anchor" => true,
      ];
      // we should ignore this payment to calculate
      DB::table('payments')->where('id', $payment->id)->update(['amount' => 0]);
      self::calculateTotal($this);
      $repayment = LoanInstallment::find($this->id);

      foreach ($paymentSchema as $feeName => $fee) {
        $feeNamePaid = $feeName ."_paid";
        if ($repayment->$feeNamePaid < $repayment->$feeName && $balance > 0) {
            $feeDebt = $repayment->$feeName - $repayment->$feeNamePaid;
            $toPay = $balance >= $feeDebt ? $feeDebt : $balance;
            $balance = $balance - $toPay;

            $items[] = [
                "index" => $count,
                "account_id" => $fee['account_id'],
                "category_id" => null,
                "type" => -1,
                "concept" => $payment->concept,
                "amount" => $toPay,
                "anchor" => true,
            ];
            $count++;
        }
      }

      // then set the payment again
      DB::table('payments')->where('id', $payment->id)->update(['amount' => $totalPaid]);
      self::calculateTotal($this);

      return $items;
    }
}
