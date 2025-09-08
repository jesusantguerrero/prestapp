<?php

namespace App\Domains\Properties\Services;

use App\Domains\CRM\Enums\ClientStatus;
use Illuminate\Support\Carbon;
use App\Domains\CRM\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Invoice\Invoice;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;

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
        max(due_date) last_invoice_date,
        monthname(due_date) invoice_month,
        clients.display_name owner_name,
        clients.names owner_first_name,
        clients.lastnames owner_lastnames,
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
      ->whereNotIn('rents.status', [Rent::STATUS_EXPIRED, Rent::STATUS_CANCELLED])
      ->where('clients.status', ClientStatus::Active->value)
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
          "owner_first_name" => $byClient->first()->owner_first_name,
          "owner_lastnames" => $byClient->first()->owner_lastnames,
          "last_invoice_date" => $byClient->max('last_invoice_date'),
          "owner_id" => $byClient->first()->owner_id,
          "total" => $byClient->sum('month_count'),
          "invoices" => $byClient,
          "totalMonths" => $byClient->count()
        ];
      })->values();
    }

    public static function pendingDrawsInvoices($teamId, $ownerId = null, $invoiceId = null) {
      $rentClass = Rent::class;
      $invoices = Invoice::selectRaw('invoices.*, DATE_FORMAT(due_date, "%Y-%m-01") invoice_month, clients.display_name owner_name, clients.names owner_first_name, clients.lastnames owner_lastname, clients.id owner_id, 
      CASE WHEN properties.name IS NOT NULL THEN properties.name ELSE rentProp.name END as property_name')
      ->where([
        'invoices.status' => 'paid'
      ])
      ->byTeam($teamId)
      ->whereIn('invoiceable_type', [Rent::class, Property::class])
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
        $query->where(fn($q) => $q->where('properties.owner_id', $ownerId)->orWhere('rents.owner_id', $ownerId));
      })
      ->leftJoin('rents', fn ($q) => $q->on('invoiceable_id', '=', 'rents.id')->where('invoiceable_type', Rent::class))
      ->leftJoin(DB::raw('properties rentProp'), 'rents.property_id', 'rentProp.id')
      ->leftJoin('properties', fn ($q) => $q->on('invoiceable_id','=' ,'properties.id')->where('invoiceable_type', Property::class))
      ->join('clients', fn ($q) => $q->on(DB::raw("CASE WHEN rents.id IS NOT NULL THEN rents.owner_id ELSE properties.owner_id END"), 'clients.id'))
      ->leftJoin('invoice_relations', 'related_invoice_id', 'invoices.id')
      ->orderByDesc('due_date')
      ->get();


      if ($ownerId) {
        return $invoices->groupBy('invoice_month')->map(fn ($months, $monthName) => [
            "monthName" => $monthName,
            "date" => $months->first()->due_date,
            "owner_name" => $months->first()->owner_name,
            "owner_first_name" => $months->first()->owner_first_name,
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
          "owner_first_name" => $byClient->first()->owner_first_name,
          "year_month" => $byClient->first()->due_date,
          "total" => $byClient->count(),
          "owners" => $owners,
          "totalMonths" => $owners->count()
        ];
      })->values();
    }

    public static function getOccupancyByMonth($teamId, $date, $ownerId = null, $invoiceIds = null) {
      $referenceDate = Carbon::createFromFormat('Y-m-d', $date);

      try {
        return DB::query()->from("property_units")->selectRaw("
          CONCAT(property_units.property_name,'-',property_units.name) unit_name,
          property_units.id,
          property_units.owner_id,
          property_units.property_id,
          property_units.property_name,
          properties.address,
          rents.id rent_id,
          rents.date,
          rents.end_date,
          rents.move_out_at,
          clients.display_name owner_name,
          rents.client_name,
          rents.status rent_status,
          SUM(invoices.total - invoices.debt) total_in_month,
          invoices.due_date invoice_month,
          invoices.status invoice_status,
          property_units.status current_status"
        )->where([
          "property_units.team_id" => $teamId,
          "property_units.is_archived" => false
        ])
        ->when($ownerId, fn($q) => $q->where("property_units.owner_id",$ownerId ))
        ->leftJoin('rents', fn ($join) =>
          $join->on('rents.unit_id', '=', 'property_units.id')
          ->where('date', '<=', $referenceDate->endOfMonth()->format('Y-m-d'))
          ->where(fn ($q) => $q->whereNull("move_out_at")->orWhere("move_out_at", ">=",  $referenceDate->startOfMonth()->format('Y-m-d')))
          ->whereNotIn('rents.status', [Rent::STATUS_EXPIRED, Rent::STATUS_CANCELLED])
        )
        ->leftJoin('invoices', fn ($join)=>
          $join->on('invoiceable_id', '=', 'rents.id')
          ->where('invoiceable_type', Rent::class)
          ->whereIn('category_type', [
            PropertyInvoiceTypes::Rent,
            PropertyInvoiceTypes::Deposit->value
          ])
          ->whereRaw('DATE_FORMAT(due_date, "%Y-%m-01") = ?', [ $referenceDate->format('Y-m-01') ])
          ->when($invoiceIds, fn($q) => $q->whereIn('invoices.id', explode(',', $invoiceIds)))
        )
        ->join('clients', 'clients.id', '=', 'property_units.owner_id')
        ->join('properties', 'properties.id', '=', 'property_units.property_id')
        ->orderByRaw("property_units.id, property_units.property_id, rents.id, CONCAT(property_units.property_name,'-',property_units.name)")
        ->groupByRaw("property_units.id, property_units.property_id, rents.id, CONCAT(property_units.property_name,'-',property_units.name)")
        ->get();
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

    public static function finishAdministration($teamId, $ownerId) {
      $owner = Client::find($ownerId);

      $properties = Property::where('owner_id', $ownerId)->get();

      foreach ($properties as $property) {
        $activeRents = $property->activeRents();

        foreach ($activeRents as $rent) {
          RentService::endTerm($rent, [], request()->user());
        }
      }

      $properties->each->close();

      $owner->update([
        'status' => ClientStatus::Inactive
      ]);
    }
}
