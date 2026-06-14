<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
  use SoftDeletes, Blameable;

  protected $table = 'employees';

  protected $fillable = [
    'user_id',
    'employee_number',
    'full_name',
    'position',
    'created_by',
    'updated_by',
    'deleted_by'
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
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
