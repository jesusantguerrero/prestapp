<?php

namespace App\Domains\Properties\Services;

use Exception;
use App\Models\User;
use App\Domains\CRM\Models\Client;
use Insane\Journal\Models\Core\Tax;
use App\Domains\Properties\Models\Rent;
use App\Notifications\InvoiceGenerated;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;

class OwnerDistributionService {
    private $client;
    private $property;

    public function __construct(Client $client, Property $property = null)
    {
      $this->client = $client;
      $this->property = $property ?? $client->properties()->first();
    }

    public function fromAutomation() {
      $invoices = $this->client->getPropertyInvoices();
      $this->storeOwnerDistribution($this->client, $invoices);
    }

    public function updateFromAutomation(int $invoiceId) {
      $ownerDrawBill = $this->client->invoices()->where('id', $invoiceId)->with(['relatedChilds'])->first();
      $invoices = $this->client->getPropertyInvoices($ownerDrawBill->id);

      $this->storeOwnerDistribution($this->client, $invoices, $ownerDrawBill);
    }

    public function fromRequest(mixed $formData) {
      $selectedInvoices = collect($formData['invoices']);
      $invoices = Invoice::whereIn('id', $selectedInvoices->pluck('id')->toArray())->get();
      $this->storeOwnerDistribution($this->client, $invoices, null, $formData);
    }

    public function fromService(mixed $invoices) {
      $this->storeOwnerDistribution($this->client, $invoices);
    }

    public function updateFromRequest(mixed $formData, int $drawBillId) {
      $ownerDrawBill = $this->client->invoices()->where('id', $drawBillId)->with(['relatedChilds'])->first();
      $selectedInvoices = collect($formData['selectedInvoices']);
      $invoices = Invoice::whereIn($selectedInvoices->pluck('id'));
      $this->storeOwnerDistribution($this->client, $invoices, $ownerDrawBill);
    }

    public function regenerateFromRequest(int $drawBillId) {
      $ownerDrawBill = $this->client->invoices()->where('id', $drawBillId)->with(['relatedChilds'])->first();
      $this->storeOwnerDistribution($this->client, $ownerDrawBill->relatedChilds, $ownerDrawBill);
    }

    public function storeOwnerDistribution(Client $client, $invoices, $ownerDrawBill = null, $formData = []) {

      [
        "items" => $items,
        "total" => $total,
        "taxTotal" => $taxTotal
      ] = self::distributionItems($invoices, $this->property);


      if (count($items)) {
        $today = date('Y-m-d');
        $documentData = [
          'concept' =>  $formData['concept'] ?? __('Property invoice'),
          'description' => $formData['description'] ?? __("Monthly rent {$client->fullName}"),
          'user_id' => $client->user_id,
          'team_id' => $client->team_id,
          'client_id' => $client->id,
          'invoiceable_id' => $client->id,
          'invoiceable_type' => Client::class,
          'invoice_account_id' => $this->property->owner_account_id, // fallback credit Account in case line items doesn't have one
          'account_id' => $this->property->owner_account_id, // Debit Account
          'date' => $formData['date'] ?? $ownerDrawBill?->date ?? date('Y-m-d'),
          'type' => Invoice::DOCUMENT_TYPE_BILL,
          'category_type' => PropertyInvoiceTypes::OwnerDistribution,
          'due_date' => $formData['due_date'] ?? $ownerDrawBill?->due_date ?? $formData['date'] ?? date('Y-m-d'),
          'subtotal' => $formData['subtotal'] ?? $total - $taxTotal,
          'total' =>  $formData['amount'] ?? $total,
          'items' => $formData['items'] ?? $items,
          'related_invoices' => [[
              "name" => PropertyInvoiceTypes::OwnerDistribution->name(),
              "items" => $invoices->map(function($invoice) {
                  return [
                    "id" => $invoice->id,
                    "description" => $invoice->category_type
                  ];
              })
            ]
          ]
        ];

        if (isset($ownerDrawBill)) {
          $ownerDrawBill->updateDocument($documentData);
        } else {
          $ownerDrawBill = Invoice::createDocument($documentData);
        }

        User::find($ownerDrawBill->user_id)->notify(new InvoiceGenerated($ownerDrawBill));

        $client->update([
          'generated_distribution_dates' => array_merge($client->generated_distribution_dates?? [], [$today])
        ]);

      }
    }

    public function recordPayment($drawBillId, $paymentData) {
      $invoice = Invoice::find($drawBillId);
      $realStateAccountId =  Account::guessAccount($invoice, ['real_state', 'cash_and_bank']);
      $error = "";

      if (!$invoice) {
          $error = __t("resource not found");
      }

      if ($invoice && $invoice->debt <= 0) {
          $error = __("This invoice is already paid");
      }

      if ($error) {
          throw new Exception($error);
      } else {
        $invoice->createPayment(array_merge(
          $paymentData,
          [
            'account_id' => $realStateAccountId,
          ]
        ));

        $invoice->save();
      }
    }

    public function distributionItems($invoices, $property) {
      $items = [];
      $total = 0;
      $taxTotal = 0;

      foreach ($invoices as $invoice) {
          $type = PropertyTransactionService::getInvoiceLineType($invoice->category_type);

          $item = [
            "name" => "$invoice->description $invoice->due_date",
            "concept" => "$invoice->description $invoice->due_date",
            "quantity" => $type * 1,
            "category_id" => $property->owner_account_id, // payment account
            "account_id" => $invoice->type == Invoice::DOCUMENT_TYPE_BILL || $invoice->category_type == PropertyInvoiceTypes::Deposit->value
              ? Account::guessAccount($property, ['real_state_reserve', 'cash_and_bank'])
              : $invoice->invoice_account_id, // debit account
            "price" => $invoice->total,
            "amount" => $type * $invoice->total,
          ];

          if ($invoice->category_type == PropertyInvoiceTypes::Rent->value) {
            $rent = $invoice->invoiceable;
            $retention = Tax::guessRetention(__("Agent payment"), $rent->commission, $invoice->toArray(), [
              "description" => __("Agent discount"),
              "account_id" => Account::guessAccount($rent, ['real_state_operative', 'cash_and_bank']),
              "translate_account_id" => Account::guessAccount($rent, [__('Rent commissions'),'expected_commissions_owners']),
            ]);

            $retentionTotal = $rent->commission_type === Rent::COMMISSION_PERCENTAGE ? (double) $retention->rate * $invoice->total / 100 : $retention->rate;

            $item['taxes'] = [
              [
                "id" => $retention->id,
                "name" => $retention->name,
                "concept" => $retention->description,
                "rate" => $retention->rate,
                "type" => $retention->type,
                "is_fixed" => $rent->commission_type === Rent::COMMISSION_FIXED,
                "label" => $retention->label,
                "description" => $retention->description,
                "amount" => $retentionTotal,
                "amount_base" => $invoice->total,
                "index" => 1,
              ]
            ];

            $taxTotal += $retentionTotal * $retention->type;
          }

          $items[] = $item;
          $total += $invoice->total;
      }

      return [
        "items" => $items,
        "total" => $total,
        "taxTotal" => $taxTotal
      ];
    }
}
