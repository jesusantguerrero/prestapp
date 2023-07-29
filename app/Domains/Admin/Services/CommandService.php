<?php

namespace App\Domains\Admin\Services;

use App\Events\Heartbeat;
use App\Models\Announcement;
use Illuminate\Support\Facades\Artisan;

class CommandService {
  public function runAdminCommand($command, $user) {
    if ($command == 'heartbeat') {
      $this->heartbeat();
    } else {
      Artisan::call($command);
      activity()
      ->causedBy($user)
      ->withProperties(['command' => $command])
      ->log("Admin ran command $command");
    }
  }

  public function heartbeat() {
    $announcement = Announcement::create([
      "title" => "Test",
      "description" => "A simple test",
      "body" => "I am just a test"
    ]);
    Heartbeat::dispatch($announcement);
  }
}
