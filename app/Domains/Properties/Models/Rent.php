<?php

namespace App\Domains\Properties\Models;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
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
        'owner_id',
        'property_id',
        'unit_id',
        'client_id',
        'client_name',
        'address',
        'deposit',
        'deposit_due',
        'date',
        'first_invoice_date',
        'next_invoice_date',
        'amount',
        'commission',
        'commission_type',
        'late_fee',
        'late_fee_type',
        'grace_days',
        'start_date',
        'move_out_at',
        'move_out_notice',
        'notes',
        'generated_invoice_dates',
        'status'
    ];

    protected $casts = [
      'generated_invoice_dates' => 'array'
    ];
    // protected
    protected $creditCategory = 'expected_payments_customers';
    protected $creditAccount = 'Customer Demand Deposits';

    protected static function boot() {
      parent::boot();
      static::creating(function ($rent) {
        $rent->next_invoice_date = $rent->next_invoice_date ?? $rent->first_invoice_date;
        $rent->client_name = $rent->client->fullName;
        $rent->address = $rent->property->address;
      });
  }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function property() {
        return $this->belongsTo(Property::class);
    }

    public function unit() {
      return $this->belongsTo(PropertyUnit::class);
    }

    public function invoices() {
      return $this->morphMany(Invoice::class, 'invoiceable');
    }

    public function rentInvoices() {
      return $this->morphMany(Invoice::class, 'invoiceable')
      ->where('invoiceable_type', Rent::class)
      ->where('category_type', PropertyInvoiceTypes::Rent);
    }

    public function depositInvoices() {
      return $this->morphMany(Invoice::class, 'invoiceable')
      ->where('invoiceable_type', Rent::class)
      ->where('category_type', PropertyInvoiceTypes::Deposit);
    }

    public function rentExpenses() {
      return $this->morphMany(Invoice::class, 'invoiceable')
      ->where('invoiceable_type', Rent::class)
      ->where('category_type', PropertyInvoiceTypes::UtilityExpense);
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
        return "Deposito de propiedad " . $this->address;
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

    // payment things
    public function getStatusField(): string {
        return 'status';
    }

    public static function calculateTotal($payable) {
        $payable->total = $payable->invoices()->sum('total');
    }

    public static function checkStatus($payable) {
            $debt = $payable->total - $payable->amount_paid;
            if ($debt == 0 && !$payable->move_out_at) {
                $status = self::STATUS_ACTIVE;
            } elseif ($debt > 0 && $debt < $payable->amount) {
                $status = self::STATUS_PARTIALLY_PAID;
            } elseif ($debt && $payable->hasLateInvoices()) {
                $status = self::STATUS_LATE;
            } elseif ($debt) {
                $status = self::STATUS_ACTIVE;
            } elseif ($payable->move_out_at) {
                $status = self::STATUS_CANCELLED;
            } else {
                $status = $payable->status;
            }
            return $status;
    }

    public function getConceptLine(): string {
        return "";
    }

    public function getTotalField($formData = []) {
      return 'total';
    }
    public function getTotal($formData = []) {
        return $this->deposit + $this->total;
    }
}
