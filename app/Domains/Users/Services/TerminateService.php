<?php

namespace App\Domains\Users\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class TerminateService
{
  public function delete(User $user): void
  {
    DB::transaction(function () use ($user) {
      $user->delete();
    });
  }

  public function restore(User $user): bool
  {
    if (! $user->trashed()) {
      return false;
    }

    DB::transaction(function () use ($user) {
      $user->restore();
    });

    return true;
  }

  public function force(User $user): bool
  {
    if (! $user->trashed()) {
      return false;
    }

    DB::transaction(function () use ($user) {
      $user->forceDelete();
    });

    return true;
  }
}
