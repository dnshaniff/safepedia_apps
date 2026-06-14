<?php

namespace App\Domains\Employees\Queries;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class IndexService
{
  public function execute(array $params): array
  {
    $user = Auth::user();

    $isAdmin = $user->username === 'administrator';

    $search = $params['search'] ?? '';

    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $baseQuery = Employee::query()->with([
      'creator:id,name',
      'editor:id,name',
      'deleter:id,name',
    ]);

    if ($isAdmin) {
      $baseQuery->withTrashed();
    }

    $totalData = (clone $baseQuery)->count();

    if (!empty($search)) {
      $baseQuery->where(function ($q) use ($search) {
        $q->where('employee_number', 'LIKE', "%{$search}%")
          ->orWhere('full_name', 'LIKE', "%{$search}%")
          ->orWhere('position', 'LIKE', "%{$search}%");
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
        'employee_number' => $row->employee_number,
        'full_name' => $row->full_name,
        'position' => $row->position,
        'hasUser' => !is_null($row->user_id),
        'creator' => $row->creator?->name ?? '-',
        'editor' => $row->editor?->name ?? '-',
        'deleter' => $row->deleter?->name ?? '-',
        'created_at' => $row->created_at,
        'updated_at' => $row->updated_at,
        'deleted_at' => $row->deleted_at,
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
