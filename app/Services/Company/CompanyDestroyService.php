<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyDestroyService
{
  public function execute(Company $company): void
  {
    DB::transaction(function () use ($company) {
      $company->delete();
    });
  }
}
