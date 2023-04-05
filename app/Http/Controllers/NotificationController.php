<?php

namespace App\Http\Controllers;

class NotificationController {

    public function index() {
        return inertia('Settings/Notifications', [
            'notifications' => request()->user()->notifications,
        ]);
    }

    public function update($notificationId = null) {
        $user = request()->user();
        if (!$notificationId) {
          $user->unreadNotifications()->update(['read_at' => now()]);
        } {
          $user->unreadNotifications()
          ->where('id', $notificationId)
          ->update(['read_at' => now()]);
        }
    }
}
