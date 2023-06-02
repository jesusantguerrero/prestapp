<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Services\AdminTeamService;
use App\Http\Controllers\InertiaController;
use App\Models\Team;
use Illuminate\Http\Request;
use Insane\Treasurer\Models\Plan;
use Insane\Treasurer\Models\Subscription;

class AdminSubscriptionController extends InertiaController
{
  protected $authorizedUser = false;
  protected $authorizedTeam = false;

  public function __construct(Subscription $subscription)
  {
      $this->model = $subscription;
      $this->searchable = ['name'];
      $this->templates = [
          "index" => 'Admin/Subscriptions/Index',
          "show" => 'Admin/Subscriptions/Show'
      ];
      $this->validationRules = [ ];
      $this->sorts = ['created_at'];
      $this->includes = [];
      $this->filters = [];
      $this->page = 1;
      $this->limit = 10;

      // $this->authorizeResource(Team::class, 'index');
      // $this->authorizeResource(Team::class, 'show');
    }
}
