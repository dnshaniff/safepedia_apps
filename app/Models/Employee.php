<?php

namespace App\Models;

use App\Http\Controllers\master\OrgUnit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
  use HasUuids, SoftDeletes;

  protected $table = 'employees';

  protected $fillable = [
    'user_id',
    'employee_code',
    'full_name',
    'hrbp_id',
    'manager_id',
    'join_date',
    'job_title_id',
    'org_unit_id',
    'employment_type',
    'office_email',
    'personal_email',
    'phone_number',
    'gender',
    'birth_of_date',
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function hrbp()
  {
    return $this->belongsTo(Employee::class, 'hrbp_id', 'id');
  }

  public function manager()
  {
    return $this->belongsTo(Employee::class, 'manager_id', 'id');
  }

  public function jobTitle()
  {
    return $this->belongsTo(JobTitle::class, 'job_title_id', 'id');
  }

  public function orgUnit()
  {
    return $this->belongsTo(OrgUnit::class, 'org_unit_id', 'id');
  }

  public function subordinates()
  {
    return $this->hasMany(Employee::class, 'manager_id', 'id');
  }

  protected $casts = [
    'join_date' => 'date',
    'birth_of_date' => 'date'
  ];
}
