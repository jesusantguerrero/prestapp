<?php

use App\Domains\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;



Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {
    Route::get('/admin', AdminController::class);
  });
