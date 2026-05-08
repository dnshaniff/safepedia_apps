<?php

namespace App\Domains\Roles\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class TerminateService
{
  public function delete(Role $role): void
  {
    DB::transaction(function () use ($role) {
      $role->delete();
    });
  }
}
