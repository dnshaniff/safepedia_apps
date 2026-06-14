<?php

namespace App\Domains\WarehouseConstructions\Services;

use App\Models\WarehouseConstruction;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CancelService
{
  public function execute(WarehouseConstruction $warehouseConstruction): WarehouseConstruction
  {
    if ($warehouseConstruction->status !== 'returned') {
      throw new RuntimeException('Only returned construction can be canceled');
    }

    $requestor = Auth::user()->employee;

    if (!$requestor) {
      throw new Exception('Logged in user does not have employee data');
    }

    return DB::transaction(function () use ($warehouseConstruction, $requestor) {
      $warehouseConstruction->approvals()->create([
        'approval_id' => null,
        'employee_id' => $requestor->id,
        'action' => 'canceled',
        'notes' => null,
      ]);

      $warehouseConstruction->update(['status' => 'canceled']);

      return $warehouseConstruction->fresh();
    });
  }
}
