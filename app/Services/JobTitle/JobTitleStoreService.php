<?php

namespace App\Services\JobTitle;

use App\Models\JobTitle;
use Illuminate\Support\Facades\DB;

class JobTitleStoreService
{
  public function execute(array $data): JobTitle
  {
    return DB::transaction(function () use ($data) {

      return JobTitle::create(['title_name' => $data['title_name']]);
    });
  }
}
