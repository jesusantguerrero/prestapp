<?php

namespace App\Domains\CRM\Models\Traits;

use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use Illuminate\Support\Facades\DB;
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
      return [
        "team_id" => $payment->team_id,
        "user_id" => $payment->user_id,
        "date" => $payment->payment_date,
        "description" => $payment->concept,
        "direction" => Transaction::DIRECTION_CREDIT,
        "total" => $payment->amount,
        "account_id" => $payment->account_id,
        "counter_account_id" => $invoice->invoice_account_id,
        "items" => $this->getTransactionItems($payment, $invoice)
      ];
    }

    protected function getTransactionItems($payment, $invoice)
    {
      $commissions = $invoice->taxesLines()->groupBy('tax_id')->select(
        DB::raw('sum(amount) as amount, concept')
      )->get();

      $commissionTotal = $commissions->sum('amount');




      // record revenue
      // $items[] = [
      //   "index" => 5,
      //   "account_id" => $payment->account_id,
      //   "category_id" => 0,
      //   "type" => -1,
      //   "concept" => $payment->concept,
      //   "amount" => $commissionTotal,
      //   "anchor" => true,
      // ];

      $items[] = [
        "index" => 0,
        "account_id" => $payment->account_id,
        "category_id" => 0,
        "type" => -1,
        "concept" => $payment->concept,
        "amount" => $payment->amount,
        "anchor" => true,
      ];

      $items[] = [
        "index" => 1,
        "account_id" => $invoice->invoice_account_id,
        "category_id" => 0,
        "type" => 1,
        "concept" => $payment->concept,
        "amount" => $payment->amount,
        "anchor" => true,
      ];

      $items[] = [
        "index" => 2,
        "account_id" => Account::guessAccount($invoice, ['Comisiones por renta','expected_commissions_owners']),
        "category_id" => 0,
        "type" => -1,
        "concept" => 'Cobro de comision ' . $invoice->concept,
        "amount" => $commissionTotal,
        "anchor" => false,
      ];

      $items[] = [
        "index" => 3,
        "account_id" => Account::guessAccount($invoice, ['real_state_operative','cash_and_bank']),
        "category_id" => 0,
        "type" => 1,
        "concept" => 'Cobro de comision ' . $invoice->concept,
        "amount" => $commissionTotal,
        "anchor" => false,
      ];

      $items[] = [
        "index" => 4,
        "account_id" => $payment->account_id,
        "category_id" => 0,
        "type" => -1,
        "concept" => $payment->concept,
        "amount" => $commissionTotal,
        "anchor" => true,
      ];

      return $items;
    }
}
