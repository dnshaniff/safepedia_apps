<?php

namespace App\Domains\Pages\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class UpdateProfileService
{
  public function execute(User $user, array $data): User
  {
    return DB::transaction(function () use ($user, $data) {
      if ($data['two_factor_enabled'] && ! $user->two_factor_enabled) {
        if (empty($data['otp'])) {
          throw ValidationException::withMessages([
            'otp' => 'Verification code is required'
          ]);
        }

        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $data['otp']);

        if (! $valid) {
          throw ValidationException::withMessages(['otp' => 'Invalid verification code']);
        }
      }

      $payload = [
        'name' => $data['name'],
        'username' => $data['username'],
        'two_factor_enabled' => $data['two_factor_enabled'],
      ];

      if (! empty($data['password'])) {
        $payload['password'] = Hash::make(
          $data['password']
        );
      }

      if (! $data['two_factor_enabled'] && $user->two_factor_enabled) {
        $payload['google2fa_secret'] = null;
      }

      $user->update($payload);

      return $user->fresh();
    });
  }
}
