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
      return back();
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

    public function downloadFile() {
      if (! Gate::allows('superadmin')) {
        abort(403);
      }

      $fileName = request()->get('fileName');

      $file = $this->backupService->getBackupFile($fileName);
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header("Cache-Control: no-cache, must-revalidate");
      header("Expires: 0");
      header('Content-Disposition: attachment; filename="'.basename($file).'"');
      header('Content-Length: ' . filesize($file));
      header('Pragma: public');

      //Clear system output buffer
      flush();

      //Read the size of the file
      readfile($file);

      //Terminate from the script
      die();
      // return response()->download($file, $fileName);
    }

}
