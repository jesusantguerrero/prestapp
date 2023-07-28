<?php


namespace App\Domains\Atmosphere\Services;

use App\Domains\Atmosphere\Models\Theme;
use App\Models\User;

class ThemeService {

    public function store(User $user, mixed $data) {
      $theme = Theme::create(array_merge([
        "user_id" => $user->id,
        "team_id" => $user->current_team_id,
      ], $data));

      return $theme;
    }

    public function getByTeamId(int $teamId) {
      return Theme::where([
        "team_id" => $teamId
      ])->first();
    }
}
