<?php

use App\Domains\Admin\Http\Controllers\AdminController;
use App\Domains\Admin\Http\Controllers\AdminTeamController;
use Illuminate\Support\Facades\Route;



Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {
    Route::get('/admin', AdminController::class);
    Route::resource('/admin/teams', AdminTeamController::class);
  });
