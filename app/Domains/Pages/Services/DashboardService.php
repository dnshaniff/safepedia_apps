<?php

namespace App\Domains\Pages\Services;

use App\Models\WarehouseConstruction;
use Carbon\Carbon;

class DashboardService
{
  public function execute(array $params): array
  {
    $search = $params['search'] ?? '';

    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $query = WarehouseConstruction::query()->with(['approvals.employee:id,full_name']);

    $totalData = (clone $query)->count();

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('construction_number', 'ILIKE', "%{$search}%")
          ->orWhere('warehouse_name', 'ILIKE', "%{$search}%");
      });
    }

    $totalFiltered = (clone $query)->count();

    $rows = $query->latest()->offset($start)->limit($length)->get();

    $data = [];

    foreach ($rows as $row) {
      $latestApproval = $row->approvals()->latest('id')->first();

      $data[] = [
        'construction_number' => $row->construction_number,
        'warehouse_name' => $row->warehouse_name,
        'grand_total_budget' => $row->grand_total_budget,
        'approval' => $latestApproval?->employee?->full_name ?? '-',
        'status' => $row->status,
        'creator' => $row->creator?->name ?? '-',
        'created_at' => $row->created_at,
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

  public function chart(string $period): array
  {
    $date = Carbon::createFromFormat('Y-m', $period);

    $daysInMonth = $date->daysInMonth;

    $categories = [];
    $series = [];

    for ($day = 1; $day <= $daysInMonth; $day++) {

      $count = WarehouseConstruction::query()->whereYear('created_at', $date->year)
        ->whereMonth('created_at', $date->month)->whereDay('created_at', $day)->count();

      $categories[] = $day;
      $series[] = $count;
    }

    return [
      'categories' => $categories,
      'series' => $series,
      'month' => $date->translatedFormat('F Y'),
    ];
  }
}
