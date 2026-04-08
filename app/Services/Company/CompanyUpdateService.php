<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyUpdateService
{
  public function execute(Company $company, array $data): Company
  {
    return DB::transaction(function () use ($company, $data) {
      $company->update([
        'company_name' => $data['company_name'],
        'company_code' => $data['company_code'],
      ]);

      return $company->refresh();
    });
  }
}
