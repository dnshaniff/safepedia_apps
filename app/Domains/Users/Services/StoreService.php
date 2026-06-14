<?php

namespace App\Domains\Users\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreService
{
  public function execute(array $data): User
  {
    return DB::transaction(function () use ($data) {
      $user = User::create([
        'name' => $data['name'],
        'username' => $data['username'],
        'password' => Hash::make($data['password']),
        'two_factor_enabled' => $data['two_factor_enabled'],
        'status' => $data['status'],
      ]);

      $user->assignRole($data['role']);

      return $user;
    });
  }
}
