<?php

namespace App\Services\OrgUnit;

use App\Models\OrgUnit;
use Illuminate\Support\Facades\DB;

class OrgUnitForceService
{
  public function execute(OrgUnit $orgUnit): bool
  {
    if (! $orgUnit->trashed()) {
      return false;
    }

    DB::transaction(function () use ($orgUnit) {
      $orgUnit->forceDelete();
    });

    return true;
  }
}
