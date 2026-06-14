<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $this->call([PermissionSeeder::class]);

    $administratorRole = Role::firstOrCreate([
      'name' => 'Administrator',
      'guard_name' => 'web',
    ]);

    $userRole = Role::firstOrCreate([
      'name' => 'User',
      'guard_name' => 'web',
    ]);

    $supervisiRole = Role::firstOrCreate([
      'name' => 'Supervisi',
      'guard_name' => 'web',
    ]);

    $administratorRole->syncPermissions(Permission::pluck('name')->toArray());

    $userRole->syncPermissions([
      'dashboard',
      'dashboard.index',
      'dashboard.chart',
      'profile.view',
      'profile.update',
      'page-warehouse_constructions',
      'warehouse_constructions.index',
      'warehouse_constructions.store',
      'warehouse_constructions.show',
      'warehouse_constructions.edit',
      'warehouse_constructions.update',
      'warehouse_constructions.destroy',
      'warehouse_constructions.submit',
      'warehouse_constructions.cancel',
    ]);

    $supervisiRole->syncPermissions([
      'dashboard',
      'dashboard.index',
      'dashboard.chart',
      'profile.view',
      'profile.update',
      'page-warehouse_constructions',
      'warehouse_constructions.index',
      'warehouse_constructions.show',
      'warehouse_constructions.approval',
    ]);

    $admin = User::firstOrCreate(
      ['username' => 'administrator'],
      [
        'name' => 'Administrator',
        'password' => Hash::make('P@ssw0rd123'),
        'status' => 'active',
      ]
    );

    $admin->update([
      'name' => 'Administrator',
      'created_by' => $admin->id,
      'updated_by' => $admin->id,
    ]);

    $admin->syncRoles(['Administrator']);

    $this->call([
      EmployeeUserSeeder::class,
      ApprovalSeeder::class,
    ]);
  }
}
