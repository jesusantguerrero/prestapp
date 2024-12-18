<?php

namespace App\Domains\Properties\Http\Controllers;

use Exception;
use Insane\Journal\Features;
use App\Domains\CRM\Models\Client;
use App\Http\Controllers\Controller;
use App\Domains\CRM\Services\ClientService;
use App\Domains\Properties\Services\RentService;
use App\Http\Controllers\Traits\HasEnrichedRequest;

class RentAgentController extends Controller
{
    use HasEnrichedRequest;

    public function list(string $viewName) {
      $teamId = request()->user()->current_team_id;
      $filters = request()->query('filters');
      $ownerId = $filters['owner'] ?? null;

      $methods = [
        "owner-draws" => fn() => ClientService::invoices($teamId, $ownerId)->paginate(),
        "commissions" => fn() => RentService::commissions($teamId)->paginate()
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

    public function showTools()
    {
      return inertia('RentAgent/Tools',
      [
          "client-portal" => Features::enabled('client-portal')
      ]);
    }

    public function sendPortalLink(Client $client)
    {
      $postData = request()->post();
      if (!$client->email || !$postData['email'])
      throw new Exception('The client has no valid email, please add an email for this client');

      if ($client->email !== $postData['email']) {
        $client->email = $postData['email'];
        $client->save();
      }

      $user = $client->findOrCreateTeamUser('owner');
      $user->sendLoginLink();

      session()->flash('success', true);
      return redirect()->back();
  }



}
