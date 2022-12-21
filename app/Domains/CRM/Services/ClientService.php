<?php

namespace App\Domains\CRM\Services;

use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Models\Loan;
use Illuminate\Database\Eloquent\Builder;
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


    public static function generateBill($client) {
      $invoices = $client->getPropertyInvoices();

      $items = [];
      $total = 0;
      foreach ($invoices as $invoice) {
        $items[] = [
              "name" => $invoice->description,
              "concept" => $invoice->description,
              "quantity" => 1,
              "price" => $invoice->total,
              "amount" => $invoice->total,
        ];
        $total += $invoice->total;
      }

      return Invoice::createDocument([
          'concept' =>  $formData['concept'] ?? 'Factura de Propiedades',
          'description' => $formData['description'] ?? "Mensualidad {$client->fullName}",
          'user_id' => $client->user_id,
          'team_id' => $client->team_id,
          'client_id' => $client->id,
          'invoiceable_id' => $client->id,
          'invoiceable_type' => Client::class,
          'date' => $formData['date'] ?? date('Y-m-d'),
          'type' => Invoice::DOCUMENT_TYPE_BILL,
          'due_date' => $formData['due_date'] ?? $formData['date'] ?? date('Y-m-d'),
          'total' =>  $formData['amount'] ?? $total,
          'items' => $formData['items'] ?? $items
      ]);
    }
}
