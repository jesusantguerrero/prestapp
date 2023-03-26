<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Services\BackupService;
use App\Http\Controllers\InertiaController;
use Illuminate\Support\Facades\Gate;

class AdminBackupController extends InertiaController
{

  public function __construct(protected BackupService $backupService)
  {
    $this->backupService = $backupService;
  }

    public function list() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }
      return inertia('Admin/BackupList', [
          'data' => $this->backupService->list(),
      ]);
    }

    public function generate() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }
      $this->backupService->generate();
    }

    public function removeFile() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }
      $fileName = request()->post('fileName');
      $this->backupService->removeFile($fileName);
    }

    public function sendFile() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }
      $fileName = request()->post('fileName');
      return  $this->backupService->sendFile($fileName);
    }

}
