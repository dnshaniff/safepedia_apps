<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAgreement extends Model
{
  use SoftDeletes;

  protected $table = 'employee_agreements';

  protected $fillable = [
    'employee_id',
    'agreement_type',
    'start_date',
    'end_date',
    'effective_date',
    'notes',
    'created_by'
  ];

  public function employee()
  {
    return $this->belongsTo(Employee::class, 'employee_id', 'id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  protected $casts = [
    'start_date',
    'end_date',
    'effective_date'
  ];
}
