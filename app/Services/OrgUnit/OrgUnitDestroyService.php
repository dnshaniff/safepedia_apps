<?php

namespace App\Services\OrgUnit;

use App\Models\OrgUnit;
use Illuminate\Support\Facades\DB;

class OrgUnitDestroyService
{
  public function execute(OrgUnit $orgUnit): void
  {
    DB::transaction(function () use ($orgUnit) {
      $orgUnit->delete();
    });
  }
}
