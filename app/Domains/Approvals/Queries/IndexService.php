<?php

namespace App\Domains\Approvals\Queries;

use App\Models\Approval;
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

    $baseQuery = Approval::query()->with(['employee', 'creator', 'editor', 'deleter']);

    if ($isAdmin) {
      $baseQuery->withTrashed();
    }

    $totalData = (clone $baseQuery)->count();

    if (!empty($search)) {
      $baseQuery->where(function ($q) use ($search) {
        $q->where('approval_role', 'LIKE', "%{$search}%")
          ->orWhere('sequence', 'LIKE', "%{$search}%")
          ->orWhereHas('employee', function ($employee) use ($search) {
            $employee->where('full_name', 'LIKE', "%{$search}%");
          });
      });
    }

    $totalFiltered = (clone $baseQuery)->count();

    $rows = $baseQuery->orderBy('sequence')->offset($start)->limit($length)->get();

    $data = [];
    $ids = $start;

    foreach ($rows as $row) {
      $data[] = [
        'fake_id' => ++$ids,
        'id' => $row->id,
        'approval_role' => $row->approval_role,
        'sequence' => $row->sequence,
        'employee' => $row->employee->full_name ?? '-',
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
