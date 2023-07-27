<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Team;
use App\Models\User;

class ApplicationConfigService {

    public function once(Team $team = null, User $user = null) {

      $applicationConfig = [];
      $sessionValue = session("$user?->id.isApplicationConfigSent");

      if($user && !$sessionValue && $team) {
        $isAdmin = config('atmosphere.superadmin.email') === $user?->email;

        $applicationConfig = [
          "isAdmin" => $isAdmin,
          "userSettings" => $team ? Setting::getSettingsByUser($team->id, $user->id) : [],
          "teamSettings" => $team ? Setting::getSettingsByUser($team->id, $user->id) : [],
          "isTeamApproved" => $isAdmin || $team?->approved_at,
        ];

        session(["$user->id.isApplicationConfigSent" => true], true);
      }

      return $applicationConfig;
    }

    public function clear(User $user) {
      cookie()->forget("$user?->id.isApplicationConfigSent");
    }

}
