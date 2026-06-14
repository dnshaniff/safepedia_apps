<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
  use SoftDeletes, Blameable, HasRoles;

  protected $fillable = [
    'name',
    'username',
    'password',
    'two_factor_enabled',
    'google2fa_secret',
    'status',
    'created_by',
    'updated_by',
    'deleted_by'
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected function casts(): array
  {
    return [
      'password' => 'hashed',
      'two_factor_enabled' => 'boolean'
    ];
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
