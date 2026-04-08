<?php

namespace App\Services\OrgUnit;

use App\Models\OrgUnit;
use Illuminate\Support\Facades\Auth;

class OrgUnitIndexService
{
  public function execute(?string $parentId): array
  {
    $user = Auth::user();

    $isAdmin = $user->username === 'administrator';

    $query = OrgUnit::query()->orderBy('sort_order');

    if ($isAdmin) {
      $query->withTrashed();
    }

    if ($parentId === null) {
      $query->whereNull('parent_id')->where('unit_type', 'Office');

      $breadcrumbs = [];
    } else {
      $query->where('parent_id', $parentId);

      $breadcrumbs = $this->buildBreadcrumbs($parentId);
    }

    $units = $query->get([
      'id',
      'unit_name',
      'unit_code',
      'unit_type',
      'deleted_at',
    ]);

    return [
      'status' => 'success',
      'data' => $units,
      'breadcrumbs' => $breadcrumbs,
    ];
  }

  private function buildBreadcrumbs(string $parentId): array
  {
    $path = OrgUnit::with('parent')->findOrFail($parentId);

    $breadcrumbs = [];

    while ($path) {
      $breadcrumbs[] = [
        'id' => $path->id,
        'name' => $path->unit_name,
      ];

      $path = $path->parent;
    }

    return array_reverse($breadcrumbs);
  }
}
