<?php

namespace App\Services\JobTitle;

use App\Models\JobTitle;
use Illuminate\Support\Facades\Auth;

class JobTitleIndexService
{
  public function execute(array $params): array
  {
    $user = Auth::user();

    $isAdmin = $user->username === 'administrator';

    $search = $params['search'] ?? '';

    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $baseQuery = JobTitle::query()->with(['creator']);

    if ($isAdmin) {
      $baseQuery->withTrashed();
    }

    $totalData = (clone $baseQuery)->count();

    if (! empty($search)) {
      $baseQuery->where(function ($q) use ($search) {
        $q->where(
          'title_name',
          'LIKE',
          "%{$search}%"
        );
      });
    }

    $totalFiltered = (clone $baseQuery)->count();

    $rows = $baseQuery->latest()->offset($start)->limit($length)->get();

    $data = [];
    $ids = $start;

    foreach ($rows as $jobTitle) {
      $data[] = [
        'fake_id' => ++$ids,
        'id' => $jobTitle->id,
        'title_name' => $jobTitle->title_name,
        'creator' => $jobTitle->creator?->display_name ?? '-',
        'editor' => $jobTitle->editor?->display_name ?? '-',
        'deleter' => $jobTitle->deleter?->display_name ?? '-',
        'created_at' => $jobTitle->created_at,
        'updated_at' => $jobTitle->updated_at,
        'deleted_at' => $jobTitle->deleted_at,
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
