<?php

namespace App\Observers;

use App\Models\EmployeeAgreement;

class EmployeeAgreementObserver
{
  public function creating(EmployeeAgreement $employeeAgreement)
  {
    if (auth()->check()) {
      $employeeAgreement->created_by = auth()->id();

      if ($employeeAgreement->agreement_type === 'Conversion') {
        $employeeAgreement->employee->update(['employment_type' => 'Colleague']);
      }
    }
  }

  public function updating(EmployeeAgreement $employeeAgreement)
  {
    if (auth()->check()) {
      if ($employeeAgreement->agreement_type === 'Conversion') {
        $employeeAgreement->employee->update(['employment_type' => 'Colleague']);
      }
    }
  }
}
