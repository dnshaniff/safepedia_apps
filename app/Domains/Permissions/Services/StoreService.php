<?php

namespace App\Domains\Permissions\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class StoreService
{
  public function execute(array $data): Permission
  {
    return DB::transaction(function () use ($data) {
      $permission = Permission::create([
        'display_name' => $data['display_name'],
        'name' => $data['name'],
        'group_name' => $data['group_name']
      ]);

      return $permission;
    });
  }
}
