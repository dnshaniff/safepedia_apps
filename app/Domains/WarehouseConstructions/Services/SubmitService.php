<?php

namespace App\Domains\WarehouseConstructions\Services;

use App\Models\Approval;
use App\Models\WarehouseConstruction;
use App\Models\WarehouseConstructionApproval;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SubmitService
{
  public function execute(WarehouseConstruction $warehouseConstruction): WarehouseConstruction
  {
    if ($warehouseConstruction->status !== 'draft') {
      throw new RuntimeException('Only draft construction can be submitted');
    }

    $employee = Auth::user()->employee;

    if (!$employee) {
      throw new Exception('Logged in user does not have employee data');
    }

    $firstApproval = Approval::orderBy('sequence')->first();

    if (!$firstApproval || !$firstApproval->employee_id) {
      throw new Exception('First approval is not configured');
    }

    return DB::transaction(function () use ($warehouseConstruction, $employee, $firstApproval) {
      WarehouseConstructionApproval::create([
        'warehouse_construction_id' => $warehouseConstruction->id,
        'approval_id' => null,
        'employee_id' => $employee->id,
        'action' => 'submitted',
        'notes' => null,
      ]);

      WarehouseConstructionApproval::create([
        'warehouse_construction_id' => $warehouseConstruction->id,
        'approval_id' => $firstApproval->id,
        'employee_id' => $firstApproval->employee_id,
        'action' => 'pending',
        'notes' => null,
      ]);

      $warehouseConstruction->update(['status' => 'pending']);

      return $warehouseConstruction->fresh();
    });
  }
}
