<?php

namespace App\Domains\Pages\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateProfileService
{
  public function execute(User $user, array $data): User
  {
    return DB::transaction(function () use ($user, $data) {
      $payload = [
        'name' => $data['name'],
        'email' => $data['email'],
        'username' => $data['username'],
      ];

      if (!empty($data['password'])) {
        $payload['password'] = Hash::make($data['password']);
      }

      $user->update($payload);

      return $user->fresh();
    });
  }
}
