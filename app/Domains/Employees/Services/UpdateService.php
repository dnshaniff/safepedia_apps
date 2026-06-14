<?php

namespace App\Domains\Employees\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class UpdateService
{
  public function execute(Employee $employee, array $data): Employee
  {
    return DB::transaction(function () use ($employee, $data) {
      $employee->update([
        'full_name' => $data['full_name'],
        'position' => $data['position'],
      ]);

      return $employee->fresh();
    });
  }
}
