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
      ['name' => 'users.store', 'display_name' => 'Store', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.edit', 'display_name' => 'Edit', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.update', 'display_name' => 'Update', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.destroy', 'display_name' => 'Destroy', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.restore', 'display_name' => 'Restore', 'group_name' => 'Users', 'guard_name' => 'web'],
      ['name' => 'users.force', 'display_name' => 'Force', 'group_name' => 'Users', 'guard_name' => 'web'],

      // Invoices
      ['name' => 'page-invoices', 'display_name' => 'View', 'group_name' => 'Invoices', 'guard_name' => 'web'],
      ['name' => 'invoices.index', 'display_name' => 'Index', 'group_name' => 'Invoices', 'guard_name' => 'web'],
      ['name' => 'invoices.store', 'display_name' => 'Store', 'group_name' => 'Invoices', 'guard_name' => 'web'],
      ['name' => 'invoices.show', 'display_name' => 'Show', 'group_name' => 'Invoices', 'guard_name' => 'web'],
      ['name' => 'invoices.edit', 'display_name' => 'Edit', 'group_name' => 'Invoices', 'guard_name' => 'web'],
      ['name' => 'invoices.update', 'display_name' => 'Update', 'group_name' => 'Invoices', 'guard_name' => 'web'],
      ['name' => 'invoices.pdf', 'display_name' => 'Export PDF', 'group_name' => 'Invoices', 'guard_name' => 'web'],
      ['name' => 'invoices.destroy', 'display_name' => 'Destroy', 'group_name' => 'Invoices', 'guard_name' => 'web'],
      ['name' => 'invoices.restore', 'display_name' => 'Restore', 'group_name' => 'Invoices', 'guard_name' => 'web'],
      ['name' => 'invoices.force', 'display_name' => 'Force', 'group_name' => 'Invoices', 'guard_name' => 'web'],

      // Invoice Payments
      ['name' => 'invoice_payments.index', 'display_name' => 'Index', 'group_name' => 'Invoice Payments', 'guard_name' => 'web'],
      ['name' => 'invoice_payments.store', 'display_name' => 'Store', 'group_name' => 'Invoice Payments', 'guard_name' => 'web'],
      ['name' => 'invoice_payments.edit', 'display_name' => 'Edit', 'group_name' => 'Invoice Payments', 'guard_name' => 'web'],
      ['name' => 'invoice_payments.update', 'display_name' => 'Update', 'group_name' => 'Invoice Payments', 'guard_name' => 'web'],
      ['name' => 'invoice_payments.destroy', 'display_name' => 'Destroy', 'group_name' => 'Invoice Payments', 'guard_name' => 'web'],

      // Articles
      ['name' => 'page-articles', 'display_name' => 'View', 'group_name' => 'Articles', 'guard_name' => 'web'],
      ['name' => 'articles.index', 'display_name' => 'Index', 'group_name' => 'Articles', 'guard_name' => 'web'],
      ['name' => 'articles.store', 'display_name' => 'Store', 'group_name' => 'Articles', 'guard_name' => 'web'],
      ['name' => 'articles.edit', 'display_name' => 'Edit', 'group_name' => 'Articles', 'guard_name' => 'web'],
      ['name' => 'articles.update', 'display_name' => 'Update', 'group_name' => 'Articles', 'guard_name' => 'web'],
      ['name' => 'articles.destroy', 'display_name' => 'Destroy', 'group_name' => 'Articles', 'guard_name' => 'web'],
      ['name' => 'articles.restore', 'display_name' => 'Restore', 'group_name' => 'Articles', 'guard_name' => 'web'],
      ['name' => 'articles.force', 'display_name' => 'Force', 'group_name' => 'Articles', 'guard_name' => 'web'],

      // Products
      ['name' => 'page-products', 'display_name' => 'View', 'group_name' => 'Products', 'guard_name' => 'web'],
      ['name' => 'products.index', 'display_name' => 'Index', 'group_name' => 'Products', 'guard_name' => 'web'],
      ['name' => 'products.store', 'display_name' => 'Store', 'group_name' => 'Products', 'guard_name' => 'web'],
      ['name' => 'products.edit', 'display_name' => 'Edit', 'group_name' => 'Products', 'guard_name' => 'web'],
      ['name' => 'products.update', 'display_name' => 'Update', 'group_name' => 'Products', 'guard_name' => 'web'],
      ['name' => 'products.destroy', 'display_name' => 'Destroy', 'group_name' => 'Products', 'guard_name' => 'web'],
      ['name' => 'products.restore', 'display_name' => 'Restore', 'group_name' => 'Products', 'guard_name' => 'web'],
      ['name' => 'products.force', 'display_name' => 'Force', 'group_name' => 'Products', 'guard_name' => 'web'],

      // Brands
      ['name' => 'page-brands', 'display_name' => 'View', 'group_name' => 'Brands', 'guard_name' => 'web'],
      ['name' => 'brands.index', 'display_name' => 'Index', 'group_name' => 'Brands', 'guard_name' => 'web'],
      ['name' => 'brands.store', 'display_name' => 'Store', 'group_name' => 'Brands', 'guard_name' => 'web'],
      ['name' => 'brands.edit', 'display_name' => 'Edit', 'group_name' => 'Brands', 'guard_name' => 'web'],
      ['name' => 'brands.update', 'display_name' => 'Update', 'group_name' => 'Brands', 'guard_name' => 'web'],
      ['name' => 'brands.destroy', 'display_name' => 'Destroy', 'group_name' => 'Brands', 'guard_name' => 'web'],
      ['name' => 'brands.restore', 'display_name' => 'Restore', 'group_name' => 'Brands', 'guard_name' => 'web'],
      ['name' => 'brands.force', 'display_name' => 'Force', 'group_name' => 'Brands', 'guard_name' => 'web'],
    ];

    usort($permissions, function ($a, $b) {
      return strcmp($a['group_name'], $b['group_name']);
    });

    foreach ($permissions as $permission) {
      Permission::create($permission);
    }
  }
}
