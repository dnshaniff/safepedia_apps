<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyForceService
{
  public function execute(Company $company): bool
  {
    if (! $company->trashed()) {
      return false;
    }

    DB::transaction(function () use ($company) {
      $company->forceDelete();
    });

    return true;
  }
}
