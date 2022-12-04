<?php

namespace App\Domains\Properties\Models;

use App\Domains\CRM\Models\Client;
use Insane\Journal\Models\Core\Transaction;
use Insane\Journal\Models\Invoice\Invoice;
use Insane\Journal\Traits\HasPaymentDocuments;
use Insane\Journal\Traits\IPayableDocument;
use Insane\Journal\Traits\Transactionable;

class Rent extends Transactionable implements IPayableDocument {
    use HasPaymentDocuments;

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_LATE =  'LATE';
    const STATUS_PAID = 'PAID';
    const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    const STATUS_GRACE = 'GRACE';
    const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'team_id',
        'user_id',
        'client_id',
        'first_installment_date',
        'amount',
        'interest_rate',
        'start_date'
    ];

    // protected
    protected $creditCategory = 'loan_line_credit';
    protected $creditAccount = 'Customer Demand Deposits';

    public function client() {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

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

    public function hasLateInvoices() {
        return $this->invoices()->late()->count();
    }

    public function getTransactionItems() {
        return [];
    }

    public static function getCategoryName($payable): string {
        return "expected_payments_customers";
    }

    public function getTransactionDescription() {
        return "Desembolso de prestamo #code";
    }

    public function getTransactionDirection() {
        return Transaction::DIRECTION_CREDIT;
    }

    public function getAccountId() {
        return $this->account_id;
    }

    public function getCounterAccountId() {
        return $this->client_account_id;
    }

    // payment things
    public function getStatusField(): string {
        return 'status';
    }

    public static function calculateTotal($payable) {
        $payable->total = $payable->invoices()->sum('amount');
    }

    public static function checkStatus($payable) {
            $debt = $payable->total - $payable->amount_paid;
            if ($debt == 0) {
                $status = self::STATUS_PAID;
            } elseif ($debt > 0 && $debt < $payable->amount) {
                $status = self::STATUS_PARTIALLY_PAID;
            } elseif ($debt && $payable->hasLateInvoices()) {
                $status = self::STATUS_LATE;
            } elseif ($debt) {
                $status = self::STATUS_ACTIVE;
            } else {
                $status = $payable->payment_status;
            }
            return $status;
    }

    public function getConceptLine(): string {
        return "";
    }

    public function getTotalField() {
        return 'total';
    }
}
