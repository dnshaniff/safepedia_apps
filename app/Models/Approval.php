<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approval extends Model
{
  use SoftDeletes, Blameable;

  protected $table = 'approvals';

  protected $fillable = [
    'sequence',
    'approval_role',
    'employee_id',
    'created_by',
    'updated_by',
    'deleted_by'
  ];

  public function employee()
  {
    return $this->belongsTo(Employee::class, 'employee_id', 'id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function editor()
  {
    return $this->belongsTo(User::class, 'updated_by', 'id');
  }

  public function deleter()
  {
    return $this->belongsTo(User::class, 'deleted_by', 'id');
  }
}
