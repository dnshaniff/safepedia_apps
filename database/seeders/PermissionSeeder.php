<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $permissions = [
      //Dashboard
      ['name' => 'dashboard', 'display_name' => 'View', 'group_name' => 'Dashboard', 'guard_name' => 'web'],
      ['name' => 'dashboard.index', 'display_name' => 'Index', 'group_name' => 'Dashboard', 'guard_name' => 'web'],
      ['name' => 'dashboard.chart', 'display_name' => 'Chart', 'group_name' => 'Dashboard', 'guard_name' => 'web'],
      ['name' => 'profile.view', 'display_name' => 'View', 'group_name' => 'Profile', 'guard_name' => 'web'],
      ['name' => 'profile.update', 'display_name' => 'Update', 'group_name' => 'Profile', 'guard_name' => 'web'],

      // Permission
      ['name' => 'access-permissions', 'display_name' => 'View', 'group_name' => 'Permissions', 'guard_name' => 'web'],
      ['name' => 'permissions.index', 'display_name' => 'Index', 'group_name' => 'Permissions', 'guard_name' => 'web'],
      ['name' => 'permissions.store', 'display_name' => 'Store', 'group_name' => 'Permissions', 'guard_name' => 'web'],
      ['name' => 'permissions.edit', 'display_name' => 'Edit', 'group_name' => 'Permissions', 'guard_name' => 'web'],
      ['name' => 'permissions.update', 'display_name' => 'Update', 'group_name' => 'Permissions', 'guard_name' => 'web'],
      ['name' => 'permissions.destroy', 'display_name' => 'Destroy', 'group_name' => 'Permissions', 'guard_name' => 'web'],

      // Roles
      ['name' => 'access-roles', 'display_name' => 'View', 'group_name' => 'Roles', 'guard_name' => 'web'],
      ['name' => 'roles.index', 'display_name' => 'Index', 'group_name' => 'Roles', 'guard_name' => 'web'],
      ['name' => 'roles.store', 'display_name' => 'Store', 'group_name' => 'Roles', 'guard_name' => 'web'],
      ['name' => 'roles.permissions', 'display_name' => 'Permissions', 'group_name' => 'Roles', 'guard_name' => 'web'],
      ['name' => 'roles.edit', 'display_name' => 'Edit', 'group_name' => 'Roles', 'guard_name' => 'web'],
      ['name' => 'roles.update', 'display_name' => 'Update', 'group_name' => 'Roles', 'guard_name' => 'web'],
      ['name' => 'roles.destroy', 'display_name' => 'Destroy', 'group_name' => 'Roles', 'guard_name' => 'web'],

      // Users
      ['name' => 'access-users', 'display_name' => 'View', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.index', 'display_name' => 'Index', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.store', 'display_name' => 'Store', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.edit', 'display_name' => 'Edit', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.update', 'display_name' => 'Update', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.destroy', 'display_name' => 'Destroy', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.restore', 'display_name' => 'Restore', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.force', 'display_name' => 'Force', 'group_name' => 'Users', 'guard_name' => 'web'],

      // Employees
      ['name' => 'page-employees', 'display_name' => 'View', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.index', 'display_name' => 'Index', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.store', 'display_name' => 'Store', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.user', 'display_name' => 'User', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.edit', 'display_name' => 'Edit', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.update', 'display_name' => 'Update', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.destroy', 'display_name' => 'Destroy', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.restore', 'display_name' => 'Restore', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.force', 'display_name' => 'Force', 'group_name' => 'Employees', 'guard_name' => 'web'],

      // Approvals
      ['name' => 'page-employees', 'display_name' => 'View', 'group_name' => 'Approvals', 'guard_name' => 'web'],
      ['name' => 'approvals.index', 'display_name' => 'Index', 'group_name' => 'Approvals', 'guard_name' => 'web'],
      ['name' => 'approvals.store', 'display_name' => 'Store', 'group_name' => 'Approvals', 'guard_name' => 'web'],
      ['name' => 'approvals.edit', 'display_name' => 'Edit', 'group_name' => 'Approvals', 'guard_name' => 'web'],
      ['name' => 'approvals.update', 'display_name' => 'Update', 'group_name' => 'Approvals', 'guard_name' => 'web'],
      ['name' => 'approvals.destroy', 'display_name' => 'Destroy', 'group_name' => 'Approvals', 'guard_name' => 'web'],
      ['name' => 'approvals.restore', 'display_name' => 'Restore', 'group_name' => 'Approvals', 'guard_name' => 'web'],
      ['name' => 'approvals.force', 'display_name' => 'Force', 'group_name' => 'Approvals', 'guard_name' => 'web'],

      // Waarehouse Construction
      ['name' => 'page-warehouse_constructions', 'display_name' => 'View', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.index', 'display_name' => 'Index', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.store', 'display_name' => 'Store', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.show', 'display_name' => 'Show', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.edit', 'display_name' => 'Edit', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.update', 'display_name' => 'Update', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.destroy', 'display_name' => 'Destroy', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.restore', 'display_name' => 'Restore', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.force', 'display_name' => 'Force', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.submit', 'display_name' => 'Submit', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.cancel', 'display_name' => 'Cancel', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
      ['name' => 'warehouse_constructions.approval', 'display_name' => 'Approval', 'group_name' => 'Warehouse Construction', 'guard_name' => 'web'],
    ];

    usort($permissions, function ($a, $b) {
      return strcmp($a['group_name'], $b['group_name']);
    });

    foreach ($permissions as $permission) {
      Permission::create($permission);
    }
  }
}
