<?php

namespace App\Domains\CRM\Services;

use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Models\Loan;
use Illuminate\Database\Eloquent\Builder;

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
}
