<?php

namespace App\Domains\Loans\Models;

use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Enums\LoanInvoiceTypes;
use App\Domains\Loans\Helpers\MetaLine;
use App\Models\User;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\PaymentDocument;
use Insane\Journal\Models\Core\Transaction;
use Insane\Journal\Models\Invoice\Invoice;
use Insane\Journal\Traits\HasPaymentDocuments;
use Insane\Journal\Traits\HasResourceAccounts;
use Insane\Journal\Traits\IPayableDocument;
use Insane\Journal\Traits\Transactionable;
use Laravel\Scout\Searchable;

class Loan extends Transactionable implements IPayableDocument {
    use HasPaymentDocuments;
    use HasResourceAccounts;
    // use Searchable;

    const STATUS_DRAFT = 'DRAFT';
    const STATUS_APPROVED ='APPROVED';
    const STATUS_DISPOSED = 'DISPOSED';
    const STATUS_PENDING = 'PENDING';
    const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    const STATUS_GRACE = 'GRACE';
    const STATUS_LATE =  'LATE';
    const STATUS_PAID = 'PAID';
    const STATUS_CANCELLED = 'CANCELLED';

    const TYPE_REFINANCE = 'REFINANCE';
    const TYPE_AGREEMENT = 'AGREEMENT';
    const TYPE_NORMAL = 'NORMAL';

    const ACTIVE_STATUSES = [
        self::STATUS_DISPOSED,
        self::STATUS_PENDING,
        self::STATUS_PARTIALLY_PAID,
        self::STATUS_GRACE,
        self::STATUS_LATE,
    ];

    protected $fillable = [
        'team_id',
        'user_id',
        'client_id',
        'original_loan_id',
        'client_name',
        'client_address',
        'date',
        'disbursement_date',
        'first_repayment_date',
        'repayment_count',
        'frequency',
        'amount',
        'amount_paid',
        'interest_rate',
        'grace_days',
        'late_fee',
        'installments_paid',
        'closing_fees',
        'category_id',
        'source_type',
        'source_account_id',
        'last_paid_at',
        'type',
        'cancel_type',
        'cancel_at_debt',
        'cancel_reason',
        'cancelled_at'
    ];


    // protected
    protected $creditCategory = 'expected_payments_customers';
    protected $creditAccount = 'Customer Demand Deposits';

    protected static function boot() {
      parent::boot();
      static::creating(function ($loan) {
          $loan->setResourceAccount('client_account_id', 'expected_payments_loans', $loan->client);
          $loan->setResourceAccount('fees_account_id', 'expected_interest_loans', $loan->client);
          $loan->late_fee_account_id = $loan->fees_account_id;
          $loan->client_name = $loan->client->fullName;
          $loan->client_address = $loan->client->address ?? $loan->client->address_details;
       
      });

      static::saving(function ($loan) {
          self::checkPayments($loan);
          self::calculateTotal($loan);
          self::checkStatus($loan);
      });
    }

    public function client() {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function installments() {
      return $this->hasMany(LoanInstallment::class);
    }

    public function sourceAccount() {
      return $this->belongsTo(Account::class, 'source_account_id');
    }

    public function agreements() {
      return $this->morphMany(Invoice::class, 'invoiceable')
      ->where('invoiceable_type', Loan::class)
      ->where('category_type', LoanInvoiceTypes::PaymentAgreement);
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

    public function scopeAgreement($query)
    {
        return $query->where('type', self::TYPE_AGREEMENT);
    }

    public function scopeRefinance($query)
    {
        return $query->where('type', self::TYPE_REFINANCE);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('payment_status', [
            self::STATUS_DISPOSED,
            self::STATUS_PENDING,
            self::STATUS_PARTIALLY_PAID,
            self::STATUS_GRACE,
            self::STATUS_LATE,
        ]);
    }

    public function hasLateInstallments() {
        return $this->installments()->late()->count();
    }

    // Transactionable config
    public function getTransactionItems() {
        return [];
    }

    public static function getCategoryName($payable): string {
        return "expected_payments_customers";
    }

    public function getTransactionDescription() {
        return "Desembolso de prestamo #code";
    }

    public function getTransactionDirection(): string {
        return Transaction::DIRECTION_CREDIT;
    }

    public function getAccountId() {
        return $this->account_id;
    }

    public function getCounterAccountId(): int {
        return $this->client_account_id;
    }

    // payable config
    public function getStatusField(): string {
        return 'payment_status';
    }

    public static function calculateTotal($payable) {
        $payable->total = $payable->installments()->sum('amount');
        $payable->amount_due = $payable->total - $payable->amount_paid;
    }

    public static function checkStatus($payable) {
      $debt = $payable->total - $payable->amount_paid;
      if ($debt <= 0 && !$payable->cancelled_at) {
          $status = self::STATUS_PAID;
      } elseif ($debt > 0 && $debt < $payable->amount) {
          $status = self::STATUS_PARTIALLY_PAID;
      } elseif ($debt && $payable->hasLateInstallments()) {
          $status = self::STATUS_LATE;
      } elseif ($debt && !$payable->cancelled_at) {
          $status = self::STATUS_PENDING;
      } elseif ($payable->cancelled_at) {
          $status = self::STATUS_CANCELLED;
      } else {
          $status = $payable->payment_status;
      }
      $payable->payment_status = $status;
    }

    public function updateStatus() {
      Loan::checkPayments($this);
      $this->save();
      Loan::calculateTotal($this);
      Loan::checkStatus($this);
    }

    public function getConceptLine(): string {
        return "";
    }

    public function getTotalField() {
        return 'total';
    }

    public function prePaymentMeta($paymentData): array {
      return  [[
        "Fecha ultima cuota pagada" => new MetaLine($this->last_paid_at ?? '', 'date'),
      ]];
    }

    public function postPaymentMeta(PaymentDocument $document): array {
      $this->updateQuietly([
        'last_paid_at' => $document->payment_date
      ]);

      return  [[
        "Cuotas atrasadas" => new MetaLine($this->installments()->late()->count()),
        "Total pagado" => new MetaLine($this->amount_paid, 'money'),
        "Balance prestamo" => new MetaLine($this->amount_due, 'money')
      ]];
    }

    // Loan info
    public function getDaysLateAttribute() {

    }
}
