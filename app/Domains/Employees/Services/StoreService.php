<?php

namespace App\Domains\Employees\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class StoreService
{
  public function execute(array $data): Employee
  {
    return DB::transaction(function () use ($data) {
      $lastEmployee = Employee::withTrashed()->lockForUpdate()->latest('id')->first();

      $nextNumber = $lastEmployee ? ((int) substr($lastEmployee->employee_number, 4)) + 1 : 1;

      $employeeNumber = 'EMP-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

      return Employee::create([
        'employee_number' => $employeeNumber,
        'full_name' => $data['full_name'],
        'position' => $data['position'],
      ]);
    });
  }
}
