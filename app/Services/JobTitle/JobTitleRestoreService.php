<?php

namespace App\Services\JobTitle;

use App\Models\JobTitle;
use Illuminate\Support\Facades\DB;

class JobTitleRestoreService
{
  public function execute(JobTitle $jobTitle): bool
  {
    if (! $jobTitle->trashed()) {
      return false;
    }

    DB::transaction(function () use ($jobTitle) {
      $jobTitle->restore();
    });

    return true;
  }
}
