<?php

namespace App\Domains\Employees\Queries;

use App\Models\Employee;

class SelectService
{
  public function execute(string $search = '', int $page = 1, int $perPage = 10): array
  {
    $query = Employee::query()->select(['id', 'full_name', 'position'])->whereHas('user');

    if ($search !== '') {
      $tokens = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];

      foreach ($tokens as $token) {
        $token = str_replace(['%', '_'], ['\\%', '\\_'], $token);
        $query->where(
          'full_name',
          'LIKE',
          "%{$token}%"
        );
      }
    }

    $query->orderBy('full_name');

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
        'text' => "{$row->full_name} - {$row->position}",
      ])->values(),
      'more' => $hasMore,
    ];
  }
}
