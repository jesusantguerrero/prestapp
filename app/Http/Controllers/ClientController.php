<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Models\Client;

class ClientController extends Controller
{
    public function index() {
        return inertia('Clients/Index', [
            'data' => Client::all()
        ]);
    }
}
