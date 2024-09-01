<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Domains\Properties\Models\Property;
use App\Http\Controllers\InertiaController;
use App\Domains\Admin\Services\CommandService;
use App\Domains\Properties\Models\PropertyUnit;

class AdminController extends InertiaController
{

  public function __construct(protected CommandService $commandService)
  {
    $this->commandService = $commandService;
  }
    // Payment Center
    public function __invoke() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }

      $data = [
        "users" => User::count(),
        "teams" => Team::count(),
        "properties" => Property::count(),
        "units" => PropertyUnit::count(),
      ];

      $widgets = [
        [
          "type" => 'Stats',
          "title" => "Estadisticas de usuarios",
          "data" => $data
        ],
        [
          "type" => 'IncomeSummary',
          "data" => $data,
          "title" => "Reportes"
        ],
        [
          "type" => 'Stats',
          "cards" => $data,
          "message" => "Propiedades"
        ],
      ];

      return inertia('Admin/Index',
      [
          'stats' => $data,
          "widgets" => $widgets,
      ]);
    }

    public function impersonateUser (int $userToImpersonate) {
      if (!Gate::allows('superadmin')) {
        abort(403);
      }
      $user = User::find($userToImpersonate);
      auth()->user()->impersonate($user, 'superadmin');
      return redirect('/dashboard');
    }

    public function leaveImpersonate() {
      auth()->user()->leaveImpersonation();
      return redirect()->route('home.index');
  }

    public function commandList() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }

      $commands = [
        [
          "label" => 'Generate Invoices',
          "command" => 'background:generate-invoices'
        ],
        [
          "label" => 'Generate Rent Late Fees',
          "command" => 'background:generate-invoices --late-fees'
        ], [
          "label" => 'Generate Owner Distributions',
          "command" => 'background:generate-owner-distributions'
        ], [
          "label" => 'Generate Loan Fees',
          "command" => 'background:generate-loan-fees'
        ], [
          "label" => 'Heartbeat',
          "command" => 'heartbeat'
        ]
      ];

      return inertia('Admin/Commands',
      [
          'data' => $commands,
      ]);
    }

    public function runCommand() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }
      $this->commandService->runAdminCommand(request()->post('command'), request()->user());
      back();
    }
}
