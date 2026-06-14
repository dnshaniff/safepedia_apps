<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeUserSeeder extends Seeder
{
  public function run(): void
  {
    $admin = User::where('username', 'administrator')->first();

    $employees = [
      [
        'employee_number' => 'EMP-000001',
        'full_name' => 'Budi',
        'position' => 'Staff Gudang',
        'username' => 'budi',
        'role' => 'User',
      ],
      [
        'employee_number' => 'EMP-000002',
        'full_name' => 'Maman',
        'position' => 'SPV Gudang',
        'username' => 'maman',
        'role' => 'Supervisi',
      ],
      [
        'employee_number' => 'EMP-000003',
        'full_name' => 'Cecep',
        'position' => 'Kepala Gudang',
        'username' => 'cecep',
        'role' => 'Supervisi',
      ],
      [
        'employee_number' => 'EMP-000004',
        'full_name' => 'Asep',
        'position' => 'Manager Operasional',
        'username' => 'asep',
        'role' => 'Supervisi',
      ],
      [
        'employee_number' => 'EMP-000005',
        'full_name' => 'Yohan',
        'position' => 'Direktur Operasional',
        'username' => 'yohan',
        'role' => 'Supervisi',
      ],
      [
        'employee_number' => 'EMP-000006',
        'full_name' => 'Yoseph',
        'position' => 'Direktur Keuangan',
        'username' => 'yoseph',
        'role' => 'Supervisi',
      ],
    ];

    foreach ($employees as $item) {
      $user = User::firstOrCreate(
        ['username' => $item['username']],
        [
          'name' => $item['full_name'],
          'password' => Hash::make('P@ssw0rd123'),
          'status' => 'active',
          'created_by' => $admin?->id,
          'updated_by' => $admin?->id,
        ]
      );

      $user->update([
        'name' => $item['full_name'],
        'status' => 'active',
        'updated_by' => $admin?->id,
      ]);

      $user->syncRoles([$item['role']]);

      Employee::updateOrCreate(
        ['employee_number' => $item['employee_number']],
        [
          'full_name' => $item['full_name'],
          'position' => $item['position'],
          'user_id' => $user->id,
          'created_by' => $admin?->id,
          'updated_by' => $admin?->id,
        ]
      );
    }
  }
}
