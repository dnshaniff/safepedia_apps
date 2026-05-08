<?php

namespace App\Domains\Roles\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UpdateService
{
  public function execute(Role $role, array $data): Role
  {
    return DB::transaction(function () use ($role, $data) {
      $role->update(['name' => $data['name']]);

      $role->syncPermissions($data['permissions']);

      return $role->fresh(['permissions']);
    });
  }
}
