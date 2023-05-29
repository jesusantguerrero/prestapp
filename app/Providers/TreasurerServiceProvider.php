<?php

namespace App\Providers;

use App\Actions\Atmosphere\ResolveBillable;
use App\Models\Team;
use Illuminate\Support\ServiceProvider;
use Insane\Treasurer\Treasurer;

class TreasurerServiceProvider extends ServiceProvider
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


    public function boot(): void
    {
        Treasurer::useCustomerModel(Team::class);
        Treasurer::useBiller(ResolveBillable::class);
    }
}
