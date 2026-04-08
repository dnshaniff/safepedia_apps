<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
  use HasRoles, HasUuids, SoftDeletes, Blameable;

  protected $fillable = [
    'username',
    'password',
    'status',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function employee()
  {
    return $this->hasOne(Employee::class, 'user_id', 'id');
  }

  public function getDisplayNameAttribute(): string
  {
    if ($this->employee) {
      return $this->employee->full_name;
    }

    if ($this->username === 'administrator') {
      return 'Administrator';
    }

    return $this->username ?? '-';
  }
}
