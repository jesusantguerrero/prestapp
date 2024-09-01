<?php

namespace App\Providers;

use App\Events\Heartbeat;
use App\Listeners\Heartbeaten;
use Illuminate\Auth\Events\Login;
use App\Listeners\HeartbeatListener;
use App\Listeners\RegisterLastLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\ClearRentInvoiceData;
use Laravel\Jetstream\Events\TeamCreated;
use Insane\Journal\Listeners\CreateTeamAccounts;
use Lab404\Impersonate\Events\TakeImpersonation;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        Login::class => [
          RegisterLastLogin::class
        ],
        TeamCreated::class => [
          CreateTeamAccounts::class,
        ],
        InvoiceDeleted::class => [
          ClearRentInvoiceData::class
        ],
        Heartbeat::class => [
          HeartbeatListener::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
      Event::listen(function (TakeImpersonation $event) {
        session()->put([
            'password_hash_sanctum' => $event->impersonated->getAuthPassword(),
        ]);
    });

    Event::listen(function (LeaveImpersonation $event) {
        session()->remove('password_hash_web');
        session()->put([
            'password_hash_sanctum' => $event->impersonator->getAuthPassword(),
        ]);
        Auth::setUser($event->impersonator);
    });
      // Event::listen(TakeImpersonation::class, fn() => $this->clearAuthHashes());
      // Event::listen(LeaveImpersonation::class, fn() => $this->clearAuthHashes());
    }

    private function clearAuthHashes() {
      foreach (array_keys(config('auth.guards')) as $guard) {
        $hashName = "password_hash_". $guard;
        session()->forget($hashName);
      }
      session()->forget(array_unique(
        [
          'password_hash_sanctum',
          'password_hash_web',
          'password_hash_'. session('impersonate.guard'),
        ]));

        session()->put([
          'password_hash_sanctum' => $event->impersonated->getAuthPassword(),
        ]);
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
