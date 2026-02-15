<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $admin = User::create([
      'username' => 'administrator',
      'password' => Hash::make('P@ssw0rd123'),
    ]);

    $this->call([PermissionSeeder::class]);

    $rolesPermissions = [
      'Administrator' => Permission::all()->pluck('name')->toArray(),
      'Restricted User' => ['dashboard'],
    ];

    foreach ($rolesPermissions as $roleName => $permissions) {
      $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
      $role->syncPermissions($permissions);
    }

    $admin->assignRole('Administrator');
  }
}
