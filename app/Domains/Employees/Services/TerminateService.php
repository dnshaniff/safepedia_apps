<?php

namespace App\Domains\Employees\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class TerminateService
{
  public function delete(Employee $employee): void
  {
    DB::transaction(function () use ($employee) {
      $employee->delete();
    });
  }

  public function restore(Employee $employee): bool
  {
    if (! $employee->trashed()) {
      return false;
    }

    DB::transaction(function () use ($employee) {
      $employee->restore();
    });

    return true;
  }

  public function force(Employee $employee): bool
  {
    if (! $employee->trashed()) {
      return false;
    }

    DB::transaction(function () use ($employee) {
      $employee->forceDelete();
    });

    return true;
  }
}
