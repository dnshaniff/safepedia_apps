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

      // Departments
      ['name' => 'master-departments', 'display_name' => 'View', 'group_name' => 'Departments', 'guard_name' => 'web'],
      ['name' => 'departments.index', 'display_name' => 'Index', 'group_name' => 'Departments', 'guard_name' => 'web'],
      ['name' => 'departments.store', 'display_name' => 'Store', 'group_name' => 'Departments', 'guard_name' => 'web'],
      ['name' => 'departments.show', 'display_name' => 'Show', 'group_name' => 'Departments', 'guard_name' => 'web'],
      ['name' => 'departments.edit', 'display_name' => 'Edit', 'group_name' => 'Departments', 'guard_name' => 'web'],
      ['name' => 'departments.update', 'display_name' => 'Update', 'group_name' => 'Departments', 'guard_name' => 'web'],
      ['name' => 'departments.destroy', 'display_name' => 'Destroy', 'group_name' => 'Departments', 'guard_name' => 'web'],
      ['name' => 'departments.restore', 'display_name' => 'Restore', 'group_name' => 'Departments', 'guard_name' => 'web'],
      ['name' => 'departments.force', 'display_name' => 'Force', 'group_name' => 'Departments', 'guard_name' => 'web'],

      // Organization Units
      ['name' => 'master-org_units', 'display_name' => 'View', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.index', 'display_name' => 'Index', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.store', 'display_name' => 'Store', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.edit', 'display_name' => 'Edit', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.update', 'display_name' => 'Update', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.destroy', 'display_name' => 'Destroy', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.restore', 'display_name' => 'Restore', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
      ['name' => 'org_units.force', 'display_name' => 'Force', 'group_name' => 'Organization Units', 'guard_name' => 'web'],
    ];

    usort($permissions, function ($a, $b) {
      return strcmp($a['group_name'], $b['group_name']);
    });

    foreach ($permissions as $permission) {
      Permission::create($permission);
    }
  }
}
