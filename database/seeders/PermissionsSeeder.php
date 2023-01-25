<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $systemAdminRole = Role::findOrCreate('system_admin');
        $adminRole = Role::findOrCreate('admin');
        $userRole = Role::findOrCreate('user');

        $dashboardAccess = $this->makePermissions('dashboard', ['finance', 'users', 'loans', 'properties']);
        $clientCrud = $this->makePermissions('clients');

        $loansCrud = $this->makePermissions('loans');
        $loanInstallmentsCrud = $this->makePermissions('loan-installments');

        $propertiesCrud = $this->makePermissions('properties');
        $unitsCrud = $this->makePermissions('units');
        $rentsCrud = $this->makePermissions('rents');

        $accountingCrud = $this->makePermissions('accounting');
        $reportsCrud = $this->makePermissions('reports');

        $permissions = array_merge(
          $dashboardAccess,
          $clientCrud,
          $loansCrud,
          $loanInstallmentsCrud,
          $propertiesCrud,
          $unitsCrud,
          $rentsCrud,
          $accountingCrud,
          $reportsCrud
        );

        $systemAdminRole->syncPermissions($permissions);
        $adminRole->syncPermissions($permissions);
        $userRole->syncPermissions($permissions);

    }

    public function makePermissions($module, $actions = ['create', 'read', 'update', 'delete']) {
      $permissions = [];

      foreach ($actions as $action) {
        $permissions[] = Permission::findOrCreate("$module.$action");
      }

      return $permissions;
    }
}
