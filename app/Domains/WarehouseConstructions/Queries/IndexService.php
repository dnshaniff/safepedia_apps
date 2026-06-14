<?php

namespace App\Domains\WarehouseConstructions\Queries;

use App\Models\WarehouseConstruction;
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

    $baseQuery = WarehouseConstruction::query()->with([
      'approvals.employee:id,full_name',
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
        $q->where('construction_number', 'LIKE', "%{$search}%")
          ->orWhere('warehouse_name', 'LIKE', "%{$search}%");
      });
    }

    $totalFiltered = (clone $baseQuery)->count();

    $rows = $baseQuery->latest()->offset($start)->limit($length)->get();

    $data = [];

    foreach ($rows as $row) {
      $pendingApproval = $row->approvals()->where('action', 'pending')->latest('id')->first();

      $lastAction = $row->approvals()->latest('id')->first();

      $approval = $pendingApproval ?: $lastAction;

      $data[] = [
        'id' => $row->id,
        'construction_number' => $row->construction_number,
        'warehouse_name' => $row->warehouse_name,
        'grand_total_budget' => $row->grand_total_budget,
        'approval' => $approval?->employee?->full_name ?? '-',
        'status' => $row->status,
        'creator' => $row->creator?->name ?? '-',
        'deleter' => $row->deleter?->name ?? '-',
        'created_at' => $row->created_at,
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
