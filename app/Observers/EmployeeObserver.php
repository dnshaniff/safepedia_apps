<?php

namespace App\Observers;

use App\Models\Employee;

class EmployeeObserver
{
  public function creating(Employee $employee)
  {
    if (auth()->check()) {
      $employee->created_by = auth()->id();
    }
  }
}
