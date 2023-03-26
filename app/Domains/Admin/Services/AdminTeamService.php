<?php

namespace App\Domains\Admin\Services;

use App\Models\Team;

class AdminTeamService {
  public function approve(Team $team) {
    $team->approved_at = now();
    $team->save();
  }
}
