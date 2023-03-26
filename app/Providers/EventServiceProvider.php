<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;
use Insane\Journal\Listeners\CreateTeamAccounts;
use Laravel\Jetstream\Events\TeamCreated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TeamCreated::class => [
            CreateTeamAccounts::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
      Event::listen(TakeImpersonation::class, fn() => $this->clearAuthHashes());
      Event::listen(LeaveImpersonation::class, fn() => $this->clearAuthHashes());
    }

    private function clearAuthHashes() {
      session()->forget(array_unique(
        [
          'password_hash_sanctum',
          'password_hash_web',
          'password_hash_'. session('impersonate.guard'),
        ]));
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
