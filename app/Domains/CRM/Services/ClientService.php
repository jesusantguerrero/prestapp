<?php 

namespace App\Domains\CRM\Services;

use App\Domains\CRM\Models\Client;

class ClientService {
    
    public static function createClient(mixed $clientData) {
        return Client::create(array_merge($clientData, [
            'display_name' => $clientData['names']
        ]));
    }
}