<?php

namespace App\Domains\Permissions\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class UpdateService
{
  public function execute(Permission $permission, array $data): Permission
  {
    return DB::transaction(function () use ($permission, $data) {
      $permission->update([
        'display_name' => $data['display_name'],
        'name' => $data['name'],
        'group_name' => $data['group_name']
      ]);

      return $permission;
    });
  }
}
