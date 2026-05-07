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

    $role = Role::firstOrCreate([
      'name' => 'Administrator',
      'guard_name' => 'web',
    ]);

    $role->syncPermissions(
      Permission::pluck('name')->toArray()
    );

    $admin = User::firstOrCreate(
      [
        'email' => 'administrator@dnalighting.co.id',
      ],
      [
        'name' => 'Administrator',
        'username' => 'administrator',
        'password' => Hash::make('P@ssw0rd123'),
      ]
    );

    $admin->update([
      'created_by' => $admin->id,
      'updated_by' => $admin->id,
    ]);

    if (! $admin->hasRole('Administrator')) {
      $admin->assignRole('Administrator');
    }
  }
}
