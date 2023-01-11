<?php

namespace App\Domains\Properties\Actions;

use App\Domains\Accounting\Helpers\InvoiceHelper;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\RentService;
use Illuminate\Support\Carbon;
use Insane\Journal\Models\Core\Tax;
use Insane\Journal\Models\Invoice\Invoice;

class GenerateInvoices {

    public static function scheduledRents() {
      $rentWithInvoicesToCreate = Rent::whereRaw('next_invoice_date - curdate() <= 31')
      ->whereNotIn('status', [Rent::STATUS_CANCELLED, Rent::STATUS_PAID])
      ->get();

      foreach ($rentWithInvoicesToCreate as $rent) {
        if (!in_array($rent->next_invoice_date, $rent->generated_invoice_dates))  {
          RentService::createInvoice([
            'date' => $rent->next_invoice_date
          ], $rent);
          $rent->update([
            'next_invoice_date' => InvoiceHelper::getNextDate($rent->next_invoice_date),
            'generated_invoice_dates' => array_merge($rent->next_invoice_date)
          ]);
        }
          }
    }

    public static function forceNextRents() {
      $rentWithInvoicesToCreate = Rent::whereNotNull('next_invoice_date')
      ->whereNotIn('status', [Rent::STATUS_CANCELLED, Rent::STATUS_PAID])
      ->get();

      foreach ($rentWithInvoicesToCreate as $rent) {
        if (!in_array($rent->next_invoice_date, $rent->generated_invoice_dates))  {
          RentService::createInvoice([
            'date' => $rent->next_invoice_date
          ], $rent);
          $rent->update([
            'next_invoice_date' => InvoiceHelper::getNextDate($rent->next_invoice_date),
            'generated_invoice_dates' => array_merge()
          ]);
        }
      }
    }

    public static function chargeLateFees() {
      $lateInvoices = Invoice::select(['invoices.*','rents.id as rentId', 'rents.grace_days as rentGraceDays'])->whereRaw('debt > 0 AND DATE_ADD(due_date, INTERVAL COALESCE(rents.grace_days, 0) DAY) < curdate()')
      ->join('rents', 'invoiceable_id', 'rents.id')
      ->where('invoiceable_type', Rent::class)
      ->whereNot('invoices.status', 'overdue')
      ->get();
      if (count($lateInvoices)) {
          self::chargeLateFee($lateInvoices);
      }
    }

    public static function chargeLateFee($invoices) {
      foreach ($invoices as $invoice) {
          $penaltyAmount = 0;

          if ($invoice->invoiceable->late_fee_type == 'PERCENTAGE') {
              $penaltyAmount = ($invoice->invoiceable->late_fee / 100) * $invoice->invoiceable->total;
          } else if ($invoice->invoiceable->late_fee_type == 'PERCENTAGE_OUTSTANDING') {
              $penaltyAmount = $invoice->invoiceable->debt;
          } else {
              $penaltyAmount = $invoice->invoiceable->late_fee;
          }

          $invoice->update([
            'status' => 'overdue'
          ]);

          $invoice->invoiceable->update([
            'status' => Rent::STATUS_LATE
          ]);

          RentService::createInvoice([
            "name" => "Factura de mora",
            "concept" => "Factura de mora {$invoice->invoiceable->client->fullName}",
            'invoice_account_id' => $invoice->invoiceable->late_fee_account_id,
            'total' => $penaltyAmount
          ], $invoice->invoiceable);

          $invoice->invoiceable->client->checkStatus();
      }
    }

    public static function forOwnerDistributions() {
      $clientWithPendingDistributions = Client::whereNotNull('owner_distribution_date')
      ->whereRaw("DATE_FORMAT(curdate(), concat('%Y-%m-', clients.owner_distribution_date)) = curdate()",)
      ->get();
      if (count($clientWithPendingDistributions)) {
        foreach ($clientWithPendingDistributions as $client) {
          if (!in_array(date('Y-m-d'), $client->generated_distribution_dates))  {
            self::ownerDistribution($client);
          }
        }
      }
    }

    public static function ownerDistribution($client, $invoiceId = null) {
      if (!$invoiceId) {
        $invoices = $client->getPropertyInvoices();
      } else {
        $existingInvoice = $client->invoices()->where('id', $invoiceId)->with(['relatedChilds'])->first();
        $invoices = $client->getPropertyInvoices($existingInvoice->id);
      }

      $items = [];
      $total = 0;
      $taxTotal = 0;
      foreach ($invoices as $invoice) {
         $type = $invoice->category_type == PropertyInvoiceTypes::DepositRefund->value ? -1 : 1;
          $item = [
            "name" => "$invoice->description $invoice->date",
            "concept" => "$invoice->description $invoice->date",
            "quantity" => 1,
            "account_id" => $invoice->invoice_account_id,
            "price" => $type * $invoice->total,
            "amount" => $type * $invoice->total,
          ];

          if ($invoice->category_type == PropertyInvoiceTypes::Rent->value) {
            $rent = $invoice->invoiceable;
            $retention = Tax::guessRetention("Commission", $rent->commission, $invoice->toArray(), [
              "description" => 'Descuento de abogado',
            ]);

            $retentionTotal = (double) $retention->rate * $invoice->total / 100;

            $item['taxes'] = [
              [
                "id" => $retention->id,
                "name" => $retention->name,
                "concept" => $retention->description,
                "rate" => $retention->rate,
                "type" => $retention->type,
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

      if (count($items)) {
        $today = date('Y-m-d');
        $clientProperty = $client->properties()->first();
        $documentData = [
          'concept' =>  $formData['concept'] ?? 'Factura de Propiedades',
          'description' => $formData['description'] ?? "Mensualidad {$client->fullName}",
          'user_id' => $client->user_id,
          'team_id' => $client->team_id,
          'client_id' => $client->id,
          'invoiceable_id' => $client->id,
          'invoiceable_type' => Client::class,
          'invoice_account_id' => $clientProperty->owner_account_id,
          'date' => $formData['date'] ?? date('Y-m-d'),
          'type' => Invoice::DOCUMENT_TYPE_BILL,
          'category_type' => PropertyInvoiceTypes::OwnerDistribution,
          'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
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

      if (isset($existingInvoice)) {
        $existingInvoice->updateDocument($documentData);
      } else {
        Invoice::createDocument($documentData);
      }

      $client->update([
        'generated_distribution_dates' => array_merge($client->generated_distribution_dates, [$today])
      ]);
      }
    }
}
