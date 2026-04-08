<?php

namespace App\Services\OrgUnit;

use App\Models\OrgUnit;

class OrgUnitSelectService
{
  public function execute(string $search = '', int $page = 1, int $perPage = 10): array
  {
    $query = OrgUnit::query()->select(['id', 'unit_name']);

    if ($search !== '') {
      $tokens = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];

      foreach ($tokens as $token) {
        $token = str_replace(['%', '_'], ['\\%', '\\_'], $token);
        $query->where(
          'unit_name',
          'LIKE',
          "%{$token}%"
        );
      }
    }

    $query->orderBy('unit_name');

    $rows = $query
      ->skip(($page - 1) * $perPage)
      ->take($perPage + 1)
      ->get();

    $hasMore = $rows->count() > $perPage;

    if ($hasMore) {
      $rows = $rows->slice(0, $perPage);
    }

    return [
      'results' => $rows->map(fn($row) => [
        'id' => $row->id,
        'text' => $row->unit_name,
      ])->values(),
      'more' => $hasMore,
    ];
  }
}
