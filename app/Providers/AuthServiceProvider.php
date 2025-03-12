<?php

namespace App\Providers;

use App\Domains\Admin\Guards\SessionGuard;
use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Policies\LoanPolicy;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
        Loan::class => LoanPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define the manage-data-quality permission
        Gate::define('manage-data-quality', function ($user) {
            return $user->hasRole('admin') || $user->hasPermissionTo('manage-data-quality');
        });
    }
}
