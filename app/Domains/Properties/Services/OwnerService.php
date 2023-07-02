<?php

namespace App\Domains\Properties\Services;

use App\Domains\Atmosphere\DTO\ReportData;
use App\Domains\Atmosphere\DTO\ReportVisualData;
use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use App\Domains\Properties\Models\Rent;
use Illuminate\Support\Carbon;
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

    public static function withPendingDraws($teamId, $invoiceId = null) {
      $invoices = Invoice::selectRaw("
        count(invoices.id) month_count,
        sum(invoices.total) as total,
        monthname(due_date) invoice_month,
        clients.display_name owner_name,
        clients.id owner_id,
        properties.name property_name
      ")
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
      ->join('rents', 'invoiceable_id', 'rents.id')
      ->join('properties', 'rents.property_id', 'properties.id')
      ->join('clients', 'rents.owner_id', 'clients.id')
      ->leftJoin('invoice_relations', 'related_invoice_id', 'invoices.id')
      ->orderByDesc('due_date')
      ->groupBy(DB::raw('owner_name, invoice_month'))
      ->get();

      return $invoices->groupBy('owner_name')->map(function ($byClient, $ownerName) {

        return [
          "owner_name" => $ownerName,
          "owner_id" => $byClient->first()->owner_id,
          "total" => $byClient->sum('month_count'),
          "invoices" => $byClient,
          "totalMonths" => $byClient->count()
        ];
      })->values();
    }

    public static function pendingDrawsInvoices($teamId, $ownerId = null, $invoiceId = null) {
      $invoices = Invoice::selectRaw('invoices.*, DATE_FORMAT(due_date, "%Y-%m-01") invoice_month, clients.display_name owner_name, clients.id owner_id, properties.name property_name')
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
      ->orderByDesc('due_date')
      ->get();

      if ($ownerId) {
        return $invoices->groupBy('invoice_month')->map(fn ($months, $monthName) => [
            "monthName" => $monthName,
            "date" => $months->first()->due_date,
            "owner_name" => $months->first()->owner_name,
            "total" => $months->sum('total'),
            "invoices" => $months,
            "totalMonths" => $months->count()
          ])->values();
      }

      return $invoices;
    }

    public static function pendingDrawsInvoicesByMonths($teamId) {
      $invoices =  OwnerService::pendingDrawsInvoices($teamId);

      return $invoices->groupBy('invoice_month')
      ->map(function ($byClient) {
        $owners = $byClient->groupBy('owner_name');

        return [
          "owner_name" => $byClient->first()->invoice_month,
          "year_month" => $byClient->first()->due_date,
          "total" => $byClient->count(),
          "owners" => $owners,
          "totalMonths" => $owners->count()
        ];
      })->values();
    }

    public static function occupancyReportByMonth($teamId, $ownerId, $date) {
      $referenceDate = Carbon::createFromFormat('Y-m-d', $date);
      try {
        $data = DB::query()->from("property_units")->selectRaw("
          CONCAT(property_units.property_name,'-',property_units.name) unit_name,
          property_units.id,
          property_units.owner_id,
          rents.id rent_id,
          rents.date,
          rents.end_date,
          rents.move_out_at,
          rents.client_name,
          rents.status rent_status,
          SUM(COALESCE(invoices.total, 0.00)) total_in_month,
          invoices.due_date invoice_month,
          property_units.status current_status"
        )->where([
          "property_units.team_id" => $teamId,
          "property_units.owner_id" => $ownerId
        ])
        ->leftJoin('rents', fn ($join) =>
          $join->on('rents.unit_id', '=', 'property_units.id')
          ->where('date', '<=', $referenceDate->endOfMonth()->format('Y-m-d'))
          ->where(fn ($q) => $q->whereNull("move_out_at")->orWhere("move_out_at", ">=",  $referenceDate->startOfMonth()->format('Y-m-d')))
        )
        ->leftJoin('invoices', fn ($join)=>
          $join->on('invoiceable_id', '=', 'rents.id')
          ->where('invoiceable_type', Rent::class)
          ->whereIn('category_type', [
            PropertyInvoiceTypes::Rent,
            PropertyInvoiceTypes::Deposit->value
          ])
          ->whereRaw('DATE_FORMAT(due_date, "%Y-%m-01") = ?', [ $referenceDate->format('Y-m-01') ])
        )
        ->orderByRaw("property_units.id, property_units.property_id, rents.id, CONCAT(property_units.property_name,'-',property_units.name)")
        ->groupByRaw("property_units.id, property_units.property_id, rents.id, CONCAT(property_units.property_name,'-',property_units.name)")
        ->get();


        return new ReportData(
          __("Occupancy report"),
          __("Rent status of the units of the owner in period"),
          $referenceDate->startOfMonth()->format('Y-m-d'),
          $referenceDate->endOfMonth()->format('Y-m-d'),
          "table",
          new ReportVisualData("table", $data),
          "",
          ""
        );
      } catch (\Exception $e) {
        dd($e->getMessage());
      }
    }

    public static function pendingDrawsCount($teamId) {
      return Invoice::where('team_id', $teamId)
      ->where('invoiceable_type', Client::class)
      ->where('category_type', PropertyInvoiceTypes::OwnerDistribution->value)
      ->unpaid()
      ->count();
    }

    public  static function payOwnerDraftAsOf($teamId, $date) {
      $months = OwnerService::pendingDrawsInvoicesByMonths($teamId)->sortBy('year_month')->values();
      echo "Months covered".count($months);

      foreach ($months as $month) {
        foreach ($month['owners'] as $ownerInvoices) {
          $owner = Client::find($ownerInvoices->first()->owner_id);
          (new OwnerDistributionService($owner))->fromService($ownerInvoices);

          $invoiceCount = count($ownerInvoices);

          activity()
          ->performedOn($owner)
          ->log("Admin generated owner distribution of {$month['invoice_month']} for $owner->display_name with {$invoiceCount}");
        }
      }
    }

}
