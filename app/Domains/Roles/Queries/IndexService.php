<?php

namespace App\Domains\Roles\Queries;

use Spatie\Permission\Models\Role;

class IndexService
{
  public function execute(array $params): array
  {
    $search = $params['search'] ?? '';

    $groupFilter = $params['group'] ?? '';

    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $baseQuery = Role::query();

    $totalData = (clone $baseQuery)->count();

    if (!empty($search)) {
      $baseQuery->where(function ($q) use ($search) {
        $q->where('name', 'LIKE', "%{$search}%");
      });
    }

    $totalFiltered = (clone $baseQuery)->count();

    $rows = $baseQuery->latest()->offset($start)->limit($length)->get();

    $data = [];
    $ids = $start;

    foreach ($rows as $row) {
      $data[] = [
        'fake_id' => ++$ids,
        'id' => $row->id,
        'name' => $row->name,
        'created_at' => $row->created_at,
        'updated_at' => $row->updated_at,
      ];
    }

    return [
      'draw' => (int) ($params['draw'] ?? 1),
      'recordsTotal' => $totalData,
      'recordsFiltered' => $totalFiltered,
      'code' => 200,
      'data' => $data,
    ];
  }
}
