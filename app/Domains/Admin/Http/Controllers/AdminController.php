<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Loans\Services\LoanService;
use App\Http\Controllers\InertiaController;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends InertiaController
{
    // Payment Center
    public function __invoke() {
      return inertia('Admin/Index',
      [
          'Teams' => Team::all(),
      ]);
    }
}
