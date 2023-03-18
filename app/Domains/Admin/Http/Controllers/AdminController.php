<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use App\Http\Controllers\InertiaController;
use App\Models\Team;
use App\Models\User;

class AdminController extends InertiaController
{
    // Payment Center
    public function __invoke() {
      return inertia('Admin/Index',
      [
          'stats' => [
            "users" => User::count(),
            "teams" => Team::count(),
            "properties" => Property::count(),
            "units" => PropertyUnit::count(),
          ],
      ]);
    }
}
