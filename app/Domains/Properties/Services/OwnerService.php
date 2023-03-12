<?php

namespace App\Domains\Properties\Services;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Rent;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Invoice\Invoice;

class OwnerService {
    public static function invoices($teamId, $clientId = null,  $statuses = []) {
      $query = DB::table('invoices')
        ->selectRaw("
        clients.display_name contact,
        clients.display_name client_name,
        clients.id contact_id,
        clients.id client_id,
        invoices.debt,
        invoices.due_date,
        invoices.id id,
        invoices.concept,
        invoices.date,
        invoices.series,
        invoices.number,
        invoices.status,
        invoices.total,
        categories.name category,
        accounts.name account_name")
        ->where([
          'invoices.team_id' => $teamId,
          'invoices.type' => Invoice::DOCUMENT_TYPE_BILL,
          'invoiceable_type' => Client::class
        ]);

        if (count($statuses)) {
          $query->whereIn('invoices.status', $statuses);
        }

        if ($clientId) {
          $query->where('invoices.client_id', $clientId);
        }

        $query
        ->join('clients', 'clients.id', '=', 'invoices.client_id')
        ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
        ->join('categories', 'categories.id', '=', 'accounts.category_id')
        ->orderByDesc('invoices.date');

        return $query;
    }

    public static function pendingDraws($teamId, $ownerId = null, $invoiceId = null) {
      $invoices = Invoice::selectRaw('invoices.*, clients.display_name owner_name, properties.name property_name')
      ->paid()
      ->byTeam($teamId)
      ->where('invoiceable_type', Rent::class)
      ->whereIn('category_type', [
        PropertyInvoiceTypes::Rent,
        PropertyInvoiceTypes::Deposit->value,
        PropertyInvoiceTypes::DepositRefund->value,
        PropertyInvoiceTypes::UtilityExpense->value,
      ])
      ->where(function ($query) use ($invoiceId) {
          $query->doesntHave('relatedParents');
          if ($invoiceId) {
            $query->orWhere('invoice_relations.invoice_id', $invoiceId);
          }
        })
      ->when($ownerId, function($query) use ($ownerId) {
        $query->where('rents.owner_id', $ownerId);
      })
      ->join('rents', 'invoiceable_id', 'rents.id')
      ->join('properties', 'rents.property_id', 'properties.id')
      ->join('clients', 'rents.owner_id', 'clients.id')
      ->leftJoin('invoice_relations', 'related_invoice_id', 'invoices.id')
      ->get()
      ->groupBy('owner_name');

      return $invoices->map(function ($byClient) {
        return [
          "owner_name" => $byClient->first()->owner_name,
          "invoices" => $byClient,
        ];
      })->values();
    }

    public static function pendingDrawsCount($teamId) {
      return Invoice::where('team_id', $teamId)
      ->where('invoiceable_type', Client::class)
      ->where('category_type', PropertyInvoiceTypes::OwnerDistribution->value)
      ->unpaid()
      ->count();
    }

}
