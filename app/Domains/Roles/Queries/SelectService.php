<?php

namespace App\Domains\Roles\Queries;

use Spatie\Permission\Models\Role;

class SelectService
{
  public function execute(string $search = '', int $page = 1, int $perPage = 10): array
  {
    $query = Role::query()->select(['id', 'name']);

    if ($search !== '') {
      $tokens = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];

      foreach ($tokens as $token) {
        $token = str_replace(['%', '_'], ['\\%', '\\_'], $token);
        $query->where(
          'name',
          'LIKE',
          "%{$token}%"
        );
      }
    }

    $query->orderBy('name');

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
        'id' => $row->name,
        'text' => $row->name,
      ])->values(),
      'more' => $hasMore,
    ];
  }
}
