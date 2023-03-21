<?php

use App\Domains\Admin\Http\Controllers\AdminController;
use App\Domains\Admin\Http\Controllers\AdminTeamController;
use Illuminate\Support\Facades\Route;

Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/', AdminController::class);
    Route::resource('/teams', AdminTeamController::class);
    Route::get('/commands', [AdminController::class, 'commandList']);
    Route::post('/commands', [AdminController::class, 'runCommand']);
});
