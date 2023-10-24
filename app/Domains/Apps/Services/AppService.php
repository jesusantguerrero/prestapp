<?php

namespace App\Domains\Apps\Services;


use App\Domains\Admin\Data\AppProfileEnum;
use App\Domains\Apps\Data\AppProfileDashboardAccounts;

class AppService
{

  public function getProfileAccounts() {
    $accounts = [
      AppProfileEnum::Renting->value => new AppProfileDashboardAccounts(
        ['real_state', 'loans', 'real_state_operative'],
        'real_state',
        'loan_business',
        ['real_state', 'loan_business', 'loans'],
       'real_state_operative'
      ),
      AppProfileEnum::SheinStore->value => new AppProfileDashboardAccounts(
        ['cash_and_bank'],
         "daily_box",
        'daily_box',
        ['cash_and_bank'],
        'daily_box'
      ),
      'icschool' => new AppProfileDashboardAccounts(
        ['cash_and_bank'],
         "daily_box",
        'daily_box',
         ['cash_and_bank'],
        'daily_box'
      )
    ];

    $appProfile = request()->user()->currentTeam->app_profile_name;
    return $accounts[$appProfile ?? AppProfileEnum::Renting->value] ?? $accounts[request()->user()->currentTeam->app_profile_name ?? AppProfileEnum::Renting->value];
  }
}
