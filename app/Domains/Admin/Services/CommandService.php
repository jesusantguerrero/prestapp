<?php

namespace App\Domains\Admin\Services;
use Illuminate\Support\Facades\Artisan;

class CommandService {
  public function runAdminCommand($command, $user) {
    Artisan::call($command);
    activity()
    ->causedBy($user)
    ->withProperties(['command' => $command])
    ->log("Admin ran command $command");
  }
}
