<?php

namespace App\Domains\WarehouseConstructions\Services;

use App\Models\WarehouseConstruction;
use App\Models\WarehouseConstructionDocument;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class UpdateService
{
  public function execute(WarehouseConstruction $warehouseConstruction, array $data): WarehouseConstruction
  {
    if (!in_array($warehouseConstruction->status, ['draft', 'returned'], true)) {
      throw new Exception('Only draft or returned construction can be updated');
    }

    $isReturned = $warehouseConstruction->status === 'returned';
    $newFiles = [];

    try {
      return DB::transaction(function () use ($warehouseConstruction, $data, &$newFiles, $isReturned) {
        $items = collect($data['item-budget']);

        $grandTotal = 0;

        $calculatedItems = $items->map(function ($item) use (&$grandTotal) {

          $quantity = (float) $item['quantity'];
          $unitPrice = (float) $item['unit_price'];

          $lineTotal = $quantity * $unitPrice;

          $grandTotal += $lineTotal;

          return [
            'item_name' => $item['item_name'],
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
          ];
        });

        $warehouseConstruction->update([
          'warehouse_name' => $data['warehouse_name'],
          'latitude' => $data['latitude'],
          'longitude' => $data['longitude'],
          'grand_total_budget' => $grandTotal,
        ]);

        if (!empty($data['removed_documents'])) {
          $documents = WarehouseConstructionDocument::whereIn('id', $data['removed_documents'])->get();

          foreach ($documents as $document) {
            Storage::disk('public')->delete($document->file_path);
            $document->delete();
          }
        }

        $lastNumber = $warehouseConstruction->documents()->get()->map(function ($document) {
          preg_match('/-(\d+)\.pdf$/', $document->file_name, $matches);

          return (int) ($matches[1] ?? 0);
        })->max();

        if (!empty($data['documents'])) {
          foreach ($data['documents'] as $index => $file) {
            $lastNumber++;
            $generatedName = Str::slug($warehouseConstruction->warehouse_name) . "-{$lastNumber}.pdf";

            $filePath = "warehouse-constructions/{$generatedName}";

            Storage::disk('public')->putFileAs('warehouse-constructions', $file, $generatedName);

            $newFiles[] = $filePath;

            $warehouseConstruction->documents()->create([
              'original_name' => $file->getClientOriginalName(),
              'file_name' => $generatedName,
              'file_path' => $filePath,
              'file_mime' => $file->getMimeType(),
              'file_size' => $file->getSize(),
            ]);
          }
        }

        $warehouseConstruction->items()->delete();

        $warehouseConstruction->items()->createMany($calculatedItems->toArray());

        if ($isReturned) {
          $requestor = Auth::user()->employee;

          if (!$requestor) {
            throw new Exception('Logged in user does not have employee data');
          }

          $returnedApprovalHistory = $warehouseConstruction
            ->approvals()
            ->where('action', 'returned')
            ->latest()
            ->first();

          if (!$returnedApprovalHistory || !$returnedApprovalHistory->approval) {
            throw new Exception('Returned approval history is not configured');
          }

          $returnedApproval = $returnedApprovalHistory->approval;

          if (!$returnedApproval->employee_id) {
            throw new Exception('Returned approval employee is not configured');
          }

          $warehouseConstruction->approvals()->create([
            'approval_id' => null,
            'employee_id' => $requestor->id,
            'action' => 'resubmitted',
            'notes' => null,
          ]);

          $warehouseConstruction->approvals()->create([
            'approval_id' => $returnedApproval->id,
            'employee_id' => $returnedApproval->employee_id,
            'action' => 'pending',
            'notes' => null,
          ]);

          $warehouseConstruction->update([
            'status' => 'pending',
          ]);
        }

        return $warehouseConstruction->fresh(['items', 'documents', 'approvals']);
      });
    } catch (Throwable $e) {
      foreach ($newFiles as $file) {
        if (Storage::disk('public')->exists($file)) {
          Storage::disk('public')->delete($file);
        }
      }

      throw $e;
    }
  }
}
