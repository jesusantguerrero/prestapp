<?php

namespace App\Domains\Apps\Data;

use Spatie\LaravelData\Data;

class AppProfileDashboardAccounts extends Data {

 public function __construct(
  public array $revenueAccounts,
  public string $dailyBox,
  public string $activity,
  public array $revenue,
  public string $commissions
  ) {}
}
