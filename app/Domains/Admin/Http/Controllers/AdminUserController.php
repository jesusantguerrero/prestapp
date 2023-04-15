<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Http\Controllers\InertiaController;
use App\Models\User;

class AdminUserController extends InertiaController
{
  protected $authorizedUser = false;
  protected $authorizedTeam = false;

  public function __construct(User $user)
  {
      $this->model = $user;
      $this->searchable = ['name'];
      $this->templates = [
          "index" => 'Admin/Users/Index',
          "show" => 'Admin/Users/Show'
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
