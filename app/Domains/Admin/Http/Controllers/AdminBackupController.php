<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Services\CommandService;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Http\Controllers\InertiaController;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AdminBackupController extends InertiaController
{

  public function __construct(protected CommandService $commandService)
  {
    $this->commandService = $commandService;
  }

    public function list() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }
      return inertia('Admin/BackupList', [
          'data' => $this->commandService->listBackups(),
      ]);
    }

    public function generate() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }
      $this->commandService->generateBackup();
    }

    public function removeFile() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }
      $fileName = request()->post('fileName');
      $this->commandService->removeBackupFile($fileName);
    }

    public function sendFile() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }
      $fileName = request()->post('fileName');
      return inertia('Admin/BackupList', [
          'data' => $this->commandService->sendBackupFile($fileName),
      ]);
    }

}
