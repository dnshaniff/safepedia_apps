<?php

namespace App\Domains\Users\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateService
{
  public function execute(User $user, array $data): User
  {
    return DB::transaction(function () use ($user, $data) {

      $auth = Auth::user();

      $payload = [
        'name' => $data['name'],
        'email' => $data['email'],
        'username' => $data['username'],
        'status' => $data['status'],
      ];

      if (!empty($data['password'])) {
        $payload['password'] = Hash::make($data['password']);
      }

      $user->update($payload);

      $user->syncRoles([$data['role']]);

      return $user->fresh(['roles']);
    });
  }
}
