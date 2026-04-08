<?php

namespace App\Services\OrgUnit;

use App\Models\OrgUnit;
use Exception;
use Illuminate\Support\Facades\DB;

class OrgUnitUpdateService
{
  public function execute(OrgUnit $orgUnit, array $data): OrgUnit
  {
    return DB::transaction(function () use ($orgUnit, $data) {

      $newParentId = isset($data['parent_id']) ? (int) $data['parent_id'] : null;

      if ($orgUnit->id === $newParentId) {
        throw new Exception('Unit cannot be parent of itself');
      }

      if ($this->isDescendant($orgUnit->id, $newParentId))
      {
        throw new Exception('Invalid hierarchy structure');
      }

      if ($orgUnit->parent_id !== $newParentId) {
        $orgUnit->parent_id = $newParentId;

        $orgUnit->sort_order =
          OrgUnit::nextSortOrder(
            $newParentId
          );
      }

      $orgUnit->update([
        'unit_name' => $data['unit_name'],
        'unit_code' => $data['unit_code'],
        'unit_type' => $data['unit_type'],
      ]);

      return $orgUnit->refresh();
    });
  }

  private function isDescendant(string $nodeId, ?string $parentId ): bool
  {
    if (! $parentId) {
      return false;
    }

    $parent = OrgUnit::find($parentId);

    while ($parent) {
      if ($parent->id === $nodeId) {
        return true;
      }

      $parent = $parent->parent;
    }

    return false;
  }
}
