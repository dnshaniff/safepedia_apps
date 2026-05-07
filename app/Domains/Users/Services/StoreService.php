<?php

namespace App\Domains\Users\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreService
{
  public function execute(array $data): User
  {
    return DB::transaction(function () use ($data) {

      $auth = Auth::user();

      $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'username' => $data['username'],
        'password' => Hash::make($data['password']),
        'status' => $data['status'],
      ]);

      $user->assignRole($data['role']);

      return $user;
    });
  }
}
