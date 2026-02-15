<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
  use HasUuids, SoftDeletes;

  protected $table = 'employees';

  protected $fillable = [
    'id',
    'user_id',
    'employee_code',
    'full_name',
    'hrbp_id',
    'manager_id',
    'join_date',
    'company_id',
    'org_unit_id',
    'job_title_id',
    'employment_status',
    'office_email',
    'personal_email',
    'phone_number',
    'gender',
    'date_of_birth',
    'ktp_number',
    'created_by'
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function company()
  {
    return $this->belongsTo(Company::class, 'company_id', 'id');
  }

  public function hrbp()
  {
    return $this->belongsTo(Employee::class, 'hrbp_id', 'id');
  }

  public function manager()
  {
    return $this->belongsTo(Employee::class, 'manager_id', 'id');
  }

  public function orgUnit()
  {
    return $this->belongsTo(OrgUnit::class, 'org_unit_id', 'id');
  }

  public function jobTitle()
  {
    return $this->belongsTo(JobTitle::class, 'job_title_id', 'id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function subordinates()
  {
    return $this->hasMany(Employee::class, 'manager_id', 'id');
  }

  public function agreements()
  {
    return $this->hasMany(EmployeeAgreement::class, 'employee_id', 'id');
  }

  public function lastDayAgreement()
  {
    return $this->hasOne(EmployeeAgreement::class, 'employee_id', 'id')
      ->whereIn('agreement_type', ['Resignation', 'Contract'])
      ->where(function ($q) {
        $q->where(function ($qq) {
          $qq->where('agreement_type', 'Resignation')->whereNotNull('effective_date');
        })->orWhere(function ($qq) {
          $qq->where('agreement_type', 'Contract')->whereNotNull('end_date');
        });
      })
      ->orderByRaw("CASE WHEN agreement_type = 'Resignation' THEN 0 ELSE 1 END")
      ->orderByRaw("COALESCE(effective_date, end_date) DESC")
      ->latest('id');
  }

  protected $casts = [
    'join_date' => 'date',
    'date_of_birth' => 'date',
    'employee_code' => 'string',
    'phone_number' => 'string',
    'ktp_number' => 'string',
  ];
}
