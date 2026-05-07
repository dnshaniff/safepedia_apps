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
        'name' => 'Administrator'
      ],
      [
        'email' => 'administrator@dna_lighting.co.id'
      ],
      [
        'username' => 'administrator',
      ],
      [
        'password' => Hash::make('P@ssw0rd123'),
      ]
    );

    if (! $admin->hasRole('Administrator')) {
      $admin->assignRole('Administrator');
    }
  }
}
