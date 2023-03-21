<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class AtmosphereImpersonateProvider extends ServiceProvider
{

  public static string $name = "atmosphere-impersonate";

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function register()
    {
      // $this->app['config']->push()
        Event::listen(TakeImpersonation::class, fn() => $this->clearAuthHashes());
        Event::listen(LeaveImpersonation::class, fn() => $this->clearAuthHashes());
    }

    public function clearAuthHashes() {
      session()->forget(array_unique([
        'password_hash_' . session('impersonate.guard'),
        'password_hash_web',
        'password_hash_sanctum'
      ]));
    }
}
