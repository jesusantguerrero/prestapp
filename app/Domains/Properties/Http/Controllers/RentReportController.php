<?php

namespace App\Domains\Properties\Http\Controllers;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Models\PropertyUnit;
use App\Domains\Properties\Models\Rent;
use App\Domains\Properties\Services\OwnerService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasEnrichedRequest;
use App\Domains\Atmosphere\Models\Setting;
use Exception;
use Illuminate\Http\Request;

class RentReportController extends Controller
{
    use HasEnrichedRequest;
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

      $invoicesByOwners = $invoices->groupBy(['category_type', 'owner_name'])
      ->map(function ($category) {
        return [
          "total" => $category->flatten()->count(),
          "owners" => $category->all(),
        ];
      });

      return inertia('Rents/Reports/RentSummary',
      [
          'invoices' => $invoicesByOwners,
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

    public function occupancy(Request $request) {
      $teamId = $request->user()->current_team_id;
      $filters = $request->query('filter');
      $ownerId = $filters['owner'] ?? null;
      $status = $filters['status'] ?? null;
      [$startDate] = $this->getFilterDates($filters);
      $units = OwnerService::occupancyByMonth($teamId, $startDate);

      $unitsByOwners = $units->groupBy(['owner_name', 'property_id'])
      ->map(function ($properties) {
        $units = $properties->flatten();
        $rented = $units
        ->filter(fn ($value) => $value->rent_id)->count();

        return [
          "total" => $units->count(),
          "propertyCount" => $properties->count(),
          "properties" => $properties->all(),
          "occupancyRate" => ($rented / $units->count()) * 100,
          "rented" => $rented,
          "vacant" => $units->count() - $rented
        ];
      });

      return inertia('Rents/Reports/Occupancy',
      [
        'invoices' => $unitsByOwners,
          "serverSearchOptions" => $this->getServerParams(),
          "section" => 'invoices'
      ]);
    }

    public function management(Request $request) {
      $teamId = $request->user()->current_team_id;
      $filters = $request->query('filters');
      $ownerId = $filters['owner'] ?? null;
      $section = $filters['section'] ?? 'bills';
      $status = $filters['status'] ?? null;
      $statuses = $status ? [$status] : [];

      $methods = [
        "bills" => fn() => ClientService::invoices($teamId, $ownerId)->get(),
        "invoices" => fn() => RentService::invoices($teamId, $statuses)
          ->orderByDesc('due_date')
          ->where('due_date', '<=', now()->timezone('America/Santo_Domingo'))
          ->get(),
        "commissions" => fn() => RentService::commissions($teamId)->get()
      ];

      $method = $methods[$section];
      $invoices = $method();

      return inertia('Properties/Transactions/ManagementTools',
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
          }),
          "section" => $section
      ]);
    }

    public function ownerStatement(int $invoiceId)
    {
      try {
        $invoice = $this->getInvoiceSecured($invoiceId, false);
        $isJson = request()->query('json');
        $withReport = request()->query('report');

        $response = [
          'invoice' => $invoice->getInvoiceData(),
          'businessData' => Setting::getByTeam($invoice->team_id),
          'type' => $invoice->type,
          'occupancy' => RentService::occupancy($invoice->team_id)
        ];

        if ($isJson) {
          return response($response, 200);
        } else {
          return inertia(config('journal.invoices_inertia_path') . '/Preview', $response);
        }
      } catch (Exception $e) {
        redirect('/invoices');
      }
    }
  }
