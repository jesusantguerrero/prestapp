<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Http\Controllers\InertiaController;
use App\Models\Team;

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
          "show" => 'Rents/Show'
      ];
      $this->validationRules = [ ];
      $this->sorts = ['created_at'];
      $this->includes = ['owner'];
      $this->filters = [];
      $this->page = 1;
      $this->limit = 10;

      $this->authorizeResource(Team::class, 'index');
  }
}
