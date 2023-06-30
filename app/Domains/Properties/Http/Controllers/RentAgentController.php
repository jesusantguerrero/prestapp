<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasEnrichedRequest;

class RentAgentController extends Controller
{
    use HasEnrichedRequest;

    public function list(string $viewName) {
      $teamId = request()->user()->current_team_id;
      $filters = request()->query('filters');
      $ownerId = $filters['owner'] ?? null;

      $methods = [
        "owner-draws" => fn() => ClientService::invoices($teamId, $ownerId)->get(),
        "commissions" => fn() => RentService::commissions($teamId)->get()
      ];

      $method = $methods[$viewName];
      $invoices = $method();

      return inertia("RentAgent/Browse",
      [
          'invoices' => $invoices,
          'outstanding' => $invoices->sum('debt'),
          'paid' => $invoices->sum(function ($invoice) {
            return $invoice->total - $invoice->debt;
          }),
          'owners' => $invoices->map(function($invoice) {
            return [
              "value" => $invoice->client_id,
              "label" => $invoice->client_name,
            ];
          })->unique('value')->values(),
          'properties' => $invoices->map(function($invoice) {
              return [
                "value" => $invoice->client_id,
                "label" => $invoice->client_name,
              ];
          })
      ]);
    }
  }
