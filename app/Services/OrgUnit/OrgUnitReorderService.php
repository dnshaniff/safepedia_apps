<?php

namespace App\Services\OrgUnit;

use App\Models\OrgUnit;
use Illuminate\Support\Facades\DB;
use Exception;

class OrgUnitReorderService
{
  public function execute(?int $parentId, array $items): void
  {
    DB::transaction(function () use ($parentId, $items) {
      $ids = collect($items) ->pluck('id')->map(fn($id) => (int) $id)->toArray();

      $units = OrgUnit::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');

      if ($units->count() !== count($ids)) {
        throw new Exception('Invalid reorder items');
      }

      foreach ($items as $index => $item) {
        $unit = $units[$item['id']];

        $unit->update([
          'sort_order' => $index + 1,
          'parent_id' => $parentId,
        ]);
      }
    });
  }
}
