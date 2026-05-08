<?php

namespace App\Domains\Permissions\Queries;

use Spatie\Permission\Models\Permission;

class IndexService
{
  public function execute(array $params): array
  {
    $search = $params['search'] ?? '';

    $groupFilter = $params['group'] ?? '';

    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $getAllPermissions = $params['getAllPermissions'] ?? false;

    if ($getAllPermissions) {
      return ['allPermissions' => Permission::pluck('name')->toArray()];
    }

    $baseQuery = Permission::query();

    $totalData = (clone $baseQuery)->count();

    if (!empty($search)) {
      $baseQuery->where(function ($q) use ($search) {
        $q->where('name', 'LIKE', "%{$search}%")
          ->orWhere('display_name', 'LIKE', "%{$search}%");
      });
    }

    if (!empty($groupFilter)) {
      $baseQuery->where('group_name', $groupFilter);
    }

    $totalFiltered = (clone $baseQuery)->count();

    $rows = $baseQuery->latest()->offset($start)->limit($length)->get();

    $data = [];
    $ids = $start;

    foreach ($rows as $permission) {
      $data[] = [
        'fake_id' => ++$ids,
        'id' => $permission->id,
        'display_name' => $permission->display_name,
        'name' => $permission->name,
        'group_name' => $permission->group_name,
        'created_at' => $permission->created_at,
        'updated_at' => $permission->updated_at,
      ];
    }

    return [
      'draw' => (int) ($params['draw'] ?? 1),
      'recordsTotal' => $totalData,
      'recordsFiltered' => $totalFiltered,
      'code' => 200,
      'data' => $data,
      'groups' => Permission::query()->select('group_name')->distinct()->orderBy('group_name')->pluck('group_name'),
    ];
  }
}
