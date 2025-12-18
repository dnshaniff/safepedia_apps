<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
  use SoftDeletes;

  protected $table = 'companies';

  protected $fillable = ['company_name', 'company_code', 'created_by'];

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
}
