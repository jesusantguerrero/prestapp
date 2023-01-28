<?php

namespace App\Domains\CRM\Models\Traits;

use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Payment;
use Insane\Journal\Models\Core\Transaction;
use Insane\Journal\Models\Invoice\Invoice;

trait OwnerTrait {
    // As property owner
    public function properties() {
      return $this->hasMany(Property::class, 'owner_id');
    }

    public function units() {
      return $this->hasManyThrough(PropertyUnit::class, Property::class, 'owner_id');
    }

    public function getPropertyInvoices($invoiceId = null) {
      return Invoice::select('invoices.*')->where([
        'rents.owner_id' => $this->id,
        'invoices.status' => 'paid'
      ])
      ->where('invoiceable_type', Rent::class)
      ->where(function ($query) use ($invoiceId) {
          $query->doesntHave('relatedParents');
          if ($invoiceId) {
            $query->orWhere('invoice_relations.invoice_id', $invoiceId);
          }
        })
      ->join('rents', 'invoiceable_id', 'rents.id')
      ->leftJoin('invoice_relations', 'related_invoice_id', 'invoices.id')
      ->get();
    }

    public function account() {
      return $this->hasOne(Account::class);
    }

    public function createPaymentTransaction(Payment $payment, Invoice $invoice) {
      $direction = $this->getTransactionDirection() ?? Transaction::DIRECTION_DEBIT;
      $counterAccountId = $this->getCounterAccountId();

      return [
        "team_id" => $payment->team_id,
        "user_id" => $payment->user_id,
        "date" => $payment->payment_date,
        "description" => $payment->concept,
        "direction" => $direction,
        "total" => $payment->amount,
        "account_id" => $payment->account_id,
        "counter_account_id" => $counterAccountId,
        "items" => []
      ];
  }
}
