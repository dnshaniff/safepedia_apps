<?php

namespace App\Domains\Approvals\Services;

use App\Models\Approval;
use Illuminate\Support\Facades\DB;

class UpdateService
{
  public function execute(Approval $approval, array $data): Approval
  {
    return DB::transaction(function () use ($approval, $data) {
      $approval->update([
        'approval_role' => $data['approval_role'],
        'sequence' => $data['sequence'],
        'employee_id' => $data['employee_id'],
      ]);

      return $approval->fresh(['employee']);
    });
  }
}
