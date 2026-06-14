<?php

namespace App\Domains\Approvals\Services;

use App\Models\Approval;
use Illuminate\Support\Facades\DB;

class StoreService
{
  public function execute(array $data): Approval
  {
    return DB::transaction(function () use ($data) {
      return Approval::create([
        'approval_role' => $data['approval_role'],
        'sequence' => $data['sequence'],
        'employee_id' => $data['employee_id'],
      ]);
    });
  }
}
