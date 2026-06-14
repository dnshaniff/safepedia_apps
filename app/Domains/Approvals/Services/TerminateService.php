<?php

namespace App\Domains\Approvals\Services;

use App\Models\Approval;
use Illuminate\Support\Facades\DB;

class TerminateService
{
  public function delete(Approval $approval): void
  {
    DB::transaction(function () use ($approval) {
      $approval->delete();
    });
  }

  public function restore(Approval $approval): bool
  {
    if (! $approval->trashed()) {
      return false;
    }

    DB::transaction(function () use ($approval) {
      $approval->restore();
    });

    return true;
  }

  public function force(Approval $approval): bool
  {
    if (! $approval->trashed()) {
      return false;
    }

    DB::transaction(function () use ($approval) {
      $approval->forceDelete();
    });

    return true;
  }
}
