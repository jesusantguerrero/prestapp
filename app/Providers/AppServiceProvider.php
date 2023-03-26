<?php

namespace App\Providers;

use App\Domains\CRM\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Insane\Journal\Models\Invoice\Invoice;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Invoice::resolveRelationUsing('client', function ($clientModel) {
        return $clientModel->belongsTo(Client::class, 'client_id');
      });

      Gate::define('superadmin', function (User $user) {
        return $user->email === config('atmosphere.superadmin.email');
      });
    }
}
