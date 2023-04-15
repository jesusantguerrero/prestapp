<?php

use App\Domains\Admin\Http\Controllers\AdminBackupController;
use App\Domains\Admin\Http\Controllers\AdminController;
use App\Domains\Admin\Http\Controllers\AdminTeamController;
use App\Domains\Admin\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Route;

Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->prefix('/admin')->name('admin.')->group(function () {
    Route::impersonate();
    Route::get('/', AdminController::class);
    Route::resource('/teams', AdminTeamController::class);
    Route::post('/teams/{team}/approve', [AdminTeamController::class, 'approve'])->name('teams.approve');
    Route::post('/impersonate-user/{userId}', [AdminController::class, 'impersonateUser']);

    Route::resource('/users', AdminUserController::class);

    Route::get('/commands', [AdminController::class, 'commandList']);
    Route::post('/commands', [AdminController::class, 'runCommand']);

    Route::get('/backups', [AdminBackupController::class, 'list']);
    Route::post('/backups', [AdminBackupController::class, 'generate']);
    Route::post('/send-backup', [AdminBackupController::class, 'sendFile']);
    Route::delete('/delete-backup', [AdminBackupController::class, 'removeFile']);
    Route::get('/backups/download', [AdminBackupController::class, 'downloadFile']);
});
