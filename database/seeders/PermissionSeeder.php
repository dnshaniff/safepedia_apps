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
      ['name' => 'users.edit', 'display_name' => 'Edit', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.update', 'display_name' => 'Update', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.destroy', 'display_name' => 'Destroy', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.restore', 'display_name' => 'Restore', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.force', 'display_name' => 'Force', 'group_name' => 'Users', 'guard_name' => 'web'],

      // Companies
      ['name' => 'masterhr-companies', 'display_name' => 'View', 'group_name' => 'Companies', 'guard_name' => 'web'],
      ['name' => 'companies.index', 'display_name' => 'Index', 'group_name' => 'Companies', 'guard_name' => 'web'],
      ['name' => 'companies.store', 'display_name' => 'Store', 'group_name' => 'Companies', 'guard_name' => 'web'],
      ['name' => 'companies.edit', 'display_name' => 'Edit', 'group_name' => 'Companies', 'guard_name' => 'web'],
      ['name' => 'companies.update', 'display_name' => 'Update', 'group_name' => 'Companies', 'guard_name' => 'web'],
      ['name' => 'companies.destroy', 'display_name' => 'Destroy', 'group_name' => 'Companies', 'guard_name' => 'web'],
      ['name' => 'companies.restore', 'display_name' => 'Restore', 'group_name' => 'Companies', 'guard_name' => 'web'],
      ['name' => 'companies.force', 'display_name' => 'Force', 'group_name' => 'Companies', 'guard_name' => 'web'],

      // Job Titles
      ['name' => 'masterhr-job_titles', 'display_name' => 'View', 'group_name' => 'Job Titles', 'guard_name' => 'web'],
      ['name' => 'job_titles.index', 'display_name' => 'Index', 'group_name' => 'Job Titles', 'guard_name' => 'web'],
      ['name' => 'job_titles.store', 'display_name' => 'Store', 'group_name' => 'Job Titles', 'guard_name' => 'web'],
      ['name' => 'job_titles.edit', 'display_name' => 'Edit', 'group_name' => 'Job Titles', 'guard_name' => 'web'],
      ['name' => 'job_titles.update', 'display_name' => 'Update', 'group_name' => 'Job Titles', 'guard_name' => 'web'],
      ['name' => 'job_titles.destroy', 'display_name' => 'Destroy', 'group_name' => 'Job Titles', 'guard_name' => 'web'],
      ['name' => 'job_titles.restore', 'display_name' => 'Restore', 'group_name' => 'Job Titles', 'guard_name' => 'web'],
      ['name' => 'job_titles.force', 'display_name' => 'Force', 'group_name' => 'Job Titles', 'guard_name' => 'web'],

      // Organization Units
      ['name' => 'masterhr-org_units', 'display_name' => 'View', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.index', 'display_name' => 'Index', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.store', 'display_name' => 'Store', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.edit', 'display_name' => 'Edit', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.update', 'display_name' => 'Update', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.destroy', 'display_name' => 'Destroy', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.reorder', 'display_name' => 'Reorder', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.restore', 'display_name' => 'Restore', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.force', 'display_name' => 'Force', 'group_name' => 'Organization Units', 'guard_name' => 'web'],

      // Employees
      ['name' => 'employee-employees', 'display_name' => 'View', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.index', 'display_name' => 'Index', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.store', 'display_name' => 'Store', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.storeUser', 'display_name' => 'Store User', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.import', 'display_name' => 'Import', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.show', 'display_name' => 'Show', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.edit', 'display_name' => 'Edit', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.update', 'display_name' => 'Update', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.destroy', 'display_name' => 'Destroy', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.restore', 'display_name' => 'Restore', 'group_name' => 'Employees', 'guard_name' => 'web'],
      ['name' => 'employees.force', 'display_name' => 'Force', 'group_name' => 'Employees', 'guard_name' => 'web'],
      // Employee Agreements
      ['name' => 'employee_agreements.index', 'display_name' => 'Index', 'group_name' => 'Employee Agreements', 'guard_name' => 'web'],
      ['name' => 'employee_agreements.store', 'display_name' => 'Store', 'group_name' => 'Employee Agreements', 'guard_name' => 'web'],
      ['name' => 'employee_agreements.edit', 'display_name' => 'Edit', 'group_name' => 'Employee Agreements', 'guard_name' => 'web'],
      ['name' => 'employee_agreements.update', 'display_name' => 'Update', 'group_name' => 'Employee Agreements', 'guard_name' => 'web'],
      ['name' => 'employee_agreements.destroy', 'display_name' => 'Destroy', 'group_name' => 'Employee Agreements', 'guard_name' => 'web'],
      ['name' => 'employee_agreements.restore', 'display_name' => 'Restore', 'group_name' => 'Employee Agreements', 'guard_name' => 'web'],
      ['name' => 'employee_agreements.force', 'display_name' => 'Force', 'group_name' => 'Employee Agreements', 'guard_name' => 'web'],

      // Upcoming Offboardings
      ['name' => 'employee-offboardings', 'display_name' => 'View', 'group_name' => 'Upcoming Offboardings', 'guard_name' => 'web'],
      ['name' => 'offboardings.index', 'display_name' => 'Index', 'group_name' => 'Upcoming Offboardings', 'guard_name' => 'web'],

      // Asset Categories
      ['name' => 'masterga-asset_categories', 'display_name' => 'View', 'group_name' => 'Asset Categories', 'guard_name' => 'web'],
      ['name' => 'asset_categories.index', 'display_name' => 'Index', 'group_name' => 'Asset Categories', 'guard_name' => 'web'],
      ['name' => 'asset_categories.store', 'display_name' => 'Store', 'group_name' => 'Asset Categories', 'guard_name' => 'web'],
      ['name' => 'asset_categories.edit', 'display_name' => 'Edit', 'group_name' => 'Asset Categories', 'guard_name' => 'web'],
      ['name' => 'asset_categories.update', 'display_name' => 'Update', 'group_name' => 'Asset Categories', 'guard_name' => 'web'],
      ['name' => 'asset_categories.destroy', 'display_name' => 'Destroy', 'group_name' => 'Asset Categories', 'guard_name' => 'web'],
      ['name' => 'asset_categories.restore', 'display_name' => 'Restore', 'group_name' => 'Asset Categories', 'guard_name' => 'web'],
      ['name' => 'asset_categories.force', 'display_name' => 'Force', 'group_name' => 'Asset Categories', 'guard_name' => 'web'],

      // Asset Types
      ['name' => 'masterga-asset_types', 'display_name' => 'View', 'group_name' => 'Asset Types', 'guard_name' => 'web'],
      ['name' => 'asset_types.index', 'display_name' => 'Index', 'group_name' => 'Asset Types', 'guard_name' => 'web'],
      ['name' => 'asset_types.store', 'display_name' => 'Store', 'group_name' => 'Asset Types', 'guard_name' => 'web'],
      ['name' => 'asset_types.edit', 'display_name' => 'Edit', 'group_name' => 'Asset Types', 'guard_name' => 'web'],
      ['name' => 'asset_types.update', 'display_name' => 'Update', 'group_name' => 'Asset Types', 'guard_name' => 'web'],
      ['name' => 'asset_types.destroy', 'display_name' => 'Destroy', 'group_name' => 'Asset Types', 'guard_name' => 'web'],
      ['name' => 'asset_types.restore', 'display_name' => 'Restore', 'group_name' => 'Asset Types', 'guard_name' => 'web'],
      ['name' => 'asset_types.force', 'display_name' => 'Force', 'group_name' => 'Asset Types', 'guard_name' => 'web'],
    ];

    usort($permissions, function ($a, $b) {
      return strcmp($a['group_name'], $b['group_name']);
    });

    foreach ($permissions as $permission) {
      Permission::create($permission);
    }
  }
}
