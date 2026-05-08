<?php

namespace App\Domains\Roles\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class StoreService
{
  public function execute(array $data): Role
  {
    return DB::transaction(function () use ($data) {
      $role = Role::create(['name' => $data['name']]);

      $role->syncPermissions($data['permissions']);

      return $role->fresh(['permissions']);
    });
  }
}
