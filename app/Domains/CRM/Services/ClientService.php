<?php

namespace App\Domains\CRM\Services;

use App\Domains\CRM\Data\ContactData;
use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Models\Loan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Invoice\Invoice;

class ClientService {

    public static function create(ContactData $clientData) {
        return Client::create(array_merge($clientData->toArray(), [
            'display_name' => $clientData['names']
        ]));
    }

    public static function ofTeam($teamId) {
        return Client::where('team_id', $teamId)->get();
    }

    public static function clientsWithActiveLoans($teamId) {
        return Client::where('team_id', $teamId)->whereHas('loans', function(Builder $query) {
            $query->whereIn('payment_status', Loan::ACTIVE_STATUSES);
        })->count();
    }

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
        invoices.description,
        invoices.category_type category,
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
}
