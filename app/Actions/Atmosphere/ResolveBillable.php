<?php

namespace App\Actions\Atmosphere;

use Illuminate\Http\Request;
use Insane\Treasurer\Contracts\BillableEntity;


class ResolveBillable implements BillableEntity
{

    public function resolve(Request $request)
    {
        return $request->user()->currentTeam;
    }

}
