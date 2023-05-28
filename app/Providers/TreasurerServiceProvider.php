<?php

namespace App\Providers;

use App\Actions\Atmosphere\ResolveBillable;
use App\Models\Team;
use Illuminate\Support\ServiceProvider;
use Insane\Treasurer\Treasurer;

class TreasurerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Treasurer::useCustomerModel(Team::class);
        Treasurer::useBiller(ResolveBillable::class);
    }
}
