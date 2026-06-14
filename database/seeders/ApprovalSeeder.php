<?php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApprovalSeeder extends Seeder
{
  public function run(): void
  {
    $admin = User::where('username', 'administrator')->first();

    $approvals = [
      ['sequence' => 1, 'approval_role' => 'SPV Gudang', 'employee' => 'Maman'],
      ['sequence' => 2, 'approval_role' => 'Kepala Gudang', 'employee' => 'Cecep'],
      ['sequence' => 3, 'approval_role' => 'Manager Operasional', 'employee' => 'Asep'],
      ['sequence' => 4, 'approval_role' => 'Direktur Operasional', 'employee' => 'Yohan'],
      ['sequence' => 5, 'approval_role' => 'Direktur Keuangan', 'employee' => 'Yoseph'],
    ];

    foreach ($approvals as $item) {
      $employee = Employee::where('full_name', $item['employee'])->first();

      Approval::updateOrCreate(
        ['sequence' => $item['sequence']],
        [
          'approval_role' => $item['approval_role'],
          'employee_id' => $employee?->id,
          'created_by' => $admin?->id,
          'updated_by' => $admin?->id,
        ]
      );
    }
  }
}
