<?php

namespace App\Services\OrgUnit;

use App\Models\OrgUnit;
use Illuminate\Support\Facades\DB;

class OrgUnitStoreService
{
  public function execute(array $data): OrgUnit
  {
    return DB::transaction(function () use ($data) {

      $parentId = $data['parent_id'] ?? null;

      $sortOrder = OrgUnit::nextSortOrder($parentId);

      $orgUnit = OrgUnit::create([
        'unit_name' => $data['unit_name'],
        'unit_code' => $data['unit_code'],
        'unit_type' => $data['unit_type'],
        'parent_id' => $parentId,
        'sort_order' => $sortOrder,
      ]);

      return $orgUnit;
    });
  }
}
