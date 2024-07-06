<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\InertiaController;
use Insane\Treasurer\Models\SubscriptionPlan;
use App\Domains\Admin\Services\AdminTeamService;

class AdminTeamController extends InertiaController
{
  protected $authorizedUser = false;
  protected $authorizedTeam = false;

  public function __construct(Team $team)
  {
      $this->model = $team;
      $this->searchable = ['name'];
      $this->templates = [
          "index" => 'Admin/Teams/Index',
          "show" => 'Admin/Teams/Show'
      ];
      $this->validationRules = [ ];
      $this->sorts = ['created_at'];
      $this->includes = ['owner'];
      $this->filters = [];
      $this->page = 1;
      $this->limit = 10;

      // $this->authorizeResource(Team::class, 'index');
      // $this->authorizeResource(Team::class, 'show');
    }

    public function approve(Team $team, AdminTeamService $teamService) {
      $teamService->approve($team);
    }


    protected function getEditProps(Request $request, $biller)
    {
      return [
        "plans" => SubscriptionPlan::orderBy('quantity')->get(),
        "subscriptions" => $biller ? $biller->subscriptions : [],
        "transactions" => []
      ];
    }
}
