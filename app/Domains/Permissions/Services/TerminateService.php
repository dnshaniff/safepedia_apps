<?php

namespace App\Domains\Permissions\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class TerminateService
{
  public function delete(Permission $permission): void
  {
    DB::transaction(function () use ($permission) {
      $permission->delete();
    });
  }
}
