<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyIndexService
{
  public function execute(array $params): array
  {
    $user = Auth::user();

    $isAdmin = $user->username === 'administrator';

    $search = $params['search'] ?? '';

    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $baseQuery = Company::query()->with(['creator']);

    if ($isAdmin) {
      $baseQuery->withTrashed();
    }

    $totalData = (clone $baseQuery)->count();

    if (! empty($search)) {
      $baseQuery->where(function ($q) use ($search) {
        $q->where(
          'company_name',
          'LIKE',
          "%{$search}%"
        )
          ->orWhere(
            'company_code',
            'LIKE',
            "%{$search}%"
          );
      });
    }

    $totalFiltered = (clone $baseQuery)->count();

    $rows = $baseQuery->latest()->offset($start)->limit($length)->get();

    $data = [];
    $ids = $start;

    foreach ($rows as $company) {
      $data[] = [
        'fake_id' => ++$ids,
        'id' => $company->id,
        'company_name' => $company->company_name,
        'company_code' => $company->company_code,
        'creator' => $company->creator?->display_name ?? '-',
        'editor' => $company->editor?->display_name ?? '-',
        'deleter' => $company->deleter?->display_name ?? '-',
        'created_at' => $company->created_at,
        'updated_at' => $company->updated_at,
        'deleted_at' => $company->deleted_at,
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
