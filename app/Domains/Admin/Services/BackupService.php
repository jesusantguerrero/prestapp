<?php

namespace App\Domains\Admin\Services;

use App\Jobs\GenerateBackup;
use App\Jobs\SendBackupEmail;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class BackupService {
  public function list() {
    $backupDir = config('app.name');
    return collect(array_diff(scandir(storage_path("app/$backupDir")), ['.', '..']))->values();
  }

  public function createFile($fileName) {
    $message  = "This is the backup for icloan";
    $from = config('atmosphere.backup.email') ?? config('atmosphere.superadmin.email');
    $to = $from;
    $backupDir = config('app.name');

    try {
      Mail::send(["text" => $message],
      ["user" => auth()->user(), "message" => $message],
      function($m) use ($to, $from, $fileName, $backupDir) {
        $m->from($from, 'ICLoan')
        ->to($to)
        ->subject("Backup for icloan " . now()->format('Y-m-d'))
        ->attach(storage_path("app/$backupDir/$fileName"));
      });

      activity()
      ->withProperties(['file' => $fileName])
      ->log("System sent the backup file $fileName");
    } catch (Exception $e) {
      activity()
      ->withProperties([
        'file' => $fileName,
        'error' => $e->getMessage()
        ])
      ->log("System failed to send  $fileName");
    }
  }

  public function sendFile($fileName) {
    SendBackupEmail::dispatch($fileName);
  }

  public function generate() {
    GenerateBackup::dispatch();
    return activity()
    ->causedBy(auth()->user())
    ->log("Admin started to generate backup");
  }

  public function removeFile($fileName) {
    $backupDir = config('app.name');

    File::delete(storage_path("app/$backupDir/$fileName"));
    return activity()
    ->causedBy(auth()->user())
    ->log("Admin removed backup file $fileName");
  }
}
