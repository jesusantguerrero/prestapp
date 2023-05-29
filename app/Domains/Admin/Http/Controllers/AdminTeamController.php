<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Services\AdminTeamService;
use App\Http\Controllers\InertiaController;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Insane\Treasurer\Contracts\BillableEntity;
use Insane\Treasurer\Models\Plan;

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
        "plans" => Plan::orderBy('quantity')->get(),
        "subscriptions" => $biller ? $biller->subscriptions : [],
        "transactions" => []
      ];
    }
}
