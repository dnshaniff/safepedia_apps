<?php

namespace App\Domains\WarehouseConstructions\Services;

use App\Models\Approval;
use App\Models\WarehouseConstruction;
use App\Models\WarehouseConstructionApproval;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
  public function execute(WarehouseConstruction $warehouseConstruction, WarehouseConstructionApproval $warehouseConstructionApproval, array $data): WarehouseConstruction
  {
    return DB::transaction(function () use ($warehouseConstruction, $warehouseConstructionApproval, $data) {
      if ($warehouseConstruction->status !== 'pending') {
        throw new Exception('Only pending construction can be approved or returned');
      }

      if ($warehouseConstructionApproval->warehouse_construction_id !== $warehouseConstruction->id) {
        throw new Exception('Approval data does not belong to this construction');
      }

      if ($warehouseConstructionApproval->action !== 'pending') {
        throw new Exception('This approval has already been processed');
      }

      if ($warehouseConstructionApproval->employee_id !== Auth::user()->employee?->id) {
        throw new Exception('You are not allowed to process this approval');
      }

      $warehouseConstructionApproval->update([
        'action' => $data['action'],
        'notes' => $data['notes'] ?? null,
      ]);

      if ($data['action'] === 'returned') {
        $warehouseConstruction->update(['status' => 'returned']);

        return $warehouseConstruction->fresh(['approvals']);
      }

      $currentSequence = $warehouseConstructionApproval->approval?->sequence;

      if (!$currentSequence) {
        throw new Exception('Current approval sequence is not configured');
      }

      $nextApproval = Approval::where('sequence', '>', $currentSequence)->orderBy('sequence')->first();

      if (!$nextApproval) {
        $warehouseConstruction->update(['status' => 'approved']);

        return $warehouseConstruction->fresh(['approvals']);
      }

      if (!$nextApproval->employee_id) {
        throw new Exception('Next approval employee is not configured');
      }

      $warehouseConstruction->approvals()->create([
        'approval_id' => $nextApproval->id,
        'employee_id' => $nextApproval->employee_id,
        'action' => 'pending',
        'notes' => null,
      ]);

      return $warehouseConstruction->fresh(['approvals']);
    });
  }
}
