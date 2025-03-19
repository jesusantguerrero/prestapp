<?php

namespace App\Domains\Properties\Services;

use App\Domains\Atmosphere\DTO\ReportData;
use App\Domains\Atmosphere\DTO\ReportVisualData;
use Illuminate\Support\Carbon;

class OwnerReportService {
    public static function occupancyMonth($teamId, $ownerId, $date, $invoiceIds) {
      $referenceDate = Carbon::createFromFormat('Y-m-d', $date);
      $data = OwnerService::getOccupancyByMonth($teamId, $date, $ownerId, $invoiceIds);

      return new ReportData(
        __("Occupancy report"),
        __("Rent status of the units of the owner in period"),
        $referenceDate->startOfMonth()->format('Y-m-d'),
        $referenceDate->endOfMonth()->format('Y-m-d'),
        "table",
        new ReportVisualData("table", $data),
        "",
        ""
      );
    }
}
