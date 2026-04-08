<?php

namespace App\Services\JobTitle;

use App\Models\JobTitle;
use Illuminate\Support\Facades\DB;

class JobTitleUpdateService
{
  public function execute(JobTitle $jobTitle, array $data): JobTitle
  {
    return DB::transaction(function () use ($jobTitle, $data) {
      $jobTitle->update(['title_name' => $data['title_name']]);

      return $jobTitle->refresh();
    });
  }
}
