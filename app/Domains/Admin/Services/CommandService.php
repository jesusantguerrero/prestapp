<?php

namespace App\Domains\Admin\Services;

use App\Jobs\SendBackupEmail;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class CommandService {
  public function runAdminCommand($command, $user) {
    Artisan::call($command);
    activity()
    ->causedBy($user)
    ->withProperties(['command' => $command])
    ->log("Admin ran command $command");
  }

  public function listBackups() {
    return collect(array_diff(scandir(storage_path('app/backup-temp')), ['.', '..']))->values();
  }

  public function createBackupFile($fileName) {
    $files = collect(array_diff(scandir(storage_path('app/backup-temp')), ['.', '..']))->values();

    $boundary      = "PHP-mixed-".md5(time());
    $boundWithPre  = "\n--".$boundary;

    $message   = $boundWithPre;
    $message  .= "\n Content-Type: text/plain; charset=UTF-8\n";
    $message  .= "\n This is the backup for icloan";

    $attachment = chunk_split(base64_encode(File::get(storage_path("app/backup-temp/$fileName"))));

    $message .= $boundWithPre;
    $message .= "\nContent-Type: application/octet-stream; name=\"".$fileName."\"";
    $message .= "\nContent-Transfer-Encoding: base64\n";
    $message .= "\nContent-Disposition: attachment\n";
    $message .= $attachment;
    $message .= $boundWithPre."--";

    $from = config('atmosphere.backup.email') ?? config('atmosphere.superadmin.email');
    $to = $from;

    $headers   = "From: $from";
    $headers  .= "\nReply-To: $from";
    $headers  .= "\nContent-Type: multipart/mixed; boundary=\"".$boundary."\"";

    try {
      mail(
        $to,
        "Backup for icloan " . now()->format('Y-m-d'),
        $message,
        $headers
      );
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
  public function sendBackupFile($fileName) {
    SendBackupEmail::dispatch($fileName);
  }
}
