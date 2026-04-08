<?php

namespace App\Services\JobTitle;

use App\Models\JobTitle;

class JobTitleSelectService
{
  public function execute(string $search = '', int $page = 1, int $perPage = 10): array
  {
    $query = JobTitle::query()->select(['id', 'title_name']);

    if ($search !== '') {
      $tokens = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];

      foreach ($tokens as $token) {
        $token = str_replace(['%', '_'], ['\\%', '\\_'], $token);
        $query->where(
          'title_name',
          'LIKE',
          "%{$token}%"
        );
      }
    }

    $query->orderBy('title_name');

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
        'text' => $row->title_name,
      ])->values(),
      'more' => $hasMore,
    ];
  }
}
