<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasEnrichedRequest;
use Illuminate\Http\Request;

class RentReportController extends Controller
{
    use HasEnrichedRequest;
    // Tools
    public function monthlySummary(Request $request) {
      $teamId = $request->user()->current_team_id;
      $filters = $request->query('filter');
      $ownerId = $filters['owner'] ?? null;
      $status = $filters['status'] ?? null;
      [$startDate, $endDate] = $this->getFilterDates($filters);
      $statuses = $status ? [$status] : [];

      $methods = [
        "bills" => fn() => ClientService::invoices($teamId, $ownerId)->get(),
        "invoices" => fn() => RentService::invoices($teamId, $statuses)
          ->orderByDesc('due_date')
          ->whereBetween('due_date', [$startDate, $endDate])
          ->get(),
        "commissions" => fn() => RentService::commissions($teamId)->get()
      ];

      $method = $methods['invoices'];
      $invoices = $method();

      return inertia('Properties/Transactions/InvoiceSummary',
      [
          'invoices' => $invoices->groupBy('category_type'),
          'outstanding' => $invoices->sum('debt'),
          'paid' => $invoices->sum(function ($invoice) {
            return $invoice->total - $invoice->debt;
          }),
          'total' => $invoices->count(),
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
          }),
          "serverSearchOptions" => $this->getServerParams(),
          "section" => 'invoices'
      ]);
    }
}
