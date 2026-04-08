<?php

namespace App\Services\JobTitle;

use App\Models\JobTitle;
use Illuminate\Support\Facades\DB;

class JobTitleDestroyService
{
  public function execute(JobTitle $jobTitle): void
  {
    DB::transaction(function () use ($jobTitle) {
      $jobTitle->delete();
    });
  }
}
