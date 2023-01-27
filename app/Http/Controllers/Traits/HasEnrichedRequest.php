<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Carbon;

trait HasEnrichedRequest {
    protected function getPostData() {
        $postData = request()->post();
        $postData['user_id'] = request()->user()->id;
        $postData['team_id'] = request()->user()->current_team_id;

        return $postData;
    }

    protected function getFilterDates($filters = [], $subCount=0) {
        $dates = isset($filters['date']) ? explode("~", $filters['date']) : [
            Carbon::now()->subMonths($subCount)->startOfMonth()->format('Y-m-d'),
            Carbon::now()->endOfMonth()->format('Y-m-d')
        ];
        return $dates;
    }
}
