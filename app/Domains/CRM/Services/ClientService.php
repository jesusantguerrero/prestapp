<?php

namespace App\Domains\CRM\Services;

use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Models\Loan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Invoice\Invoice;

class ClientService {

    public static function createClient(mixed $clientData) {
        return Client::create(array_merge($clientData, [
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

    public static function invoices($teamId, $statuses = []) {
      $query = DB::table('invoices')
        ->selectRaw("
        clients.display_name contact,
        clients.display_name client_name,
        clients.id contact_id,
        invoices.debt,
        invoices.due_date,
        invoices.id id,
        invoices.concept,
        invoices.date,
        invoices.series,
        invoices.number,
        invoices.status,
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

        $query
        ->join('clients', 'clients.id', '=', 'invoices.client_id')
        ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
        ->join('categories', 'categories.id', '=', 'accounts.category_id')
        ->take(5);

        return $query;
    }
}
