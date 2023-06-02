<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Services\AdminTeamService;
use App\Http\Controllers\InertiaController;
use App\Models\Team;
use Insane\Treasurer\Models\Plan;
use Insane\Treasurer\BillingService;

class AdminBillingController
{
    public function subscribe(Team $team, int $planId, BillingService $treasurerService)
    {
        $treasurerService->subscribe($planId, $team->owner, $team);
        return redirect()->back();
    }
}
