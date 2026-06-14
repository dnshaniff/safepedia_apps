<?php

namespace App\Domains\Employees\Services;

use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
  public function execute(Employee $employee, array $data): User
  {
    return DB::transaction(function () use ($employee, $data) {
      if ($employee->user_id) {
        throw new Exception('Employee already has a user account');
      }

      $user = User::create([
        'name' => $employee->full_name,
        'username' => $data['username'],
        'password' => Hash::make($data['password']),
      ]);

      $user->assignRole($data['role']);

      $employee->update(['user_id' => $user->id]);

      return $user;
    });
  }
}
