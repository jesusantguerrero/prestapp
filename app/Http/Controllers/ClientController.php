<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Models\Client;
use App\Domains\CRM\Services\ClientService;
use App\Http\Requests\ClientRequest;

class ClientController extends Controller
{
    public function index() {
        return inertia('Clients/Index', [
            'data' => Client::all()
        ]);
    }

    public function store(ClientRequest $clientFormRequest) {
        $validatedData = $clientFormRequest->validated();
        
        ClientService::createClient(array_merge($validatedData, [
            'team_id' => $clientFormRequest->user()->current_team_id,
            'user_id' => $clientFormRequest->user()->id
        ]));
    }
}
