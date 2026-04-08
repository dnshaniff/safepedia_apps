<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyStoreService
{
  public function execute(array $data): Company
  {
    return DB::transaction(function () use ($data) {

      return Company::create([
        'company_name' => $data['company_name'],
        'company_code' => $data['company_code'],
      ]);
    });
  }
}
