<?php

namespace App\Domains\WarehouseConstructions\Services;

use App\Models\WarehouseConstruction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class StoreService
{
  public function execute(array $data): WarehouseConstruction
  {
    $storedFiles = [];

    try {
      return DB::transaction(function () use ($data, &$storedFiles) {
        $items = collect($data['item-budget']);

        $grandTotal = 0;

        $calculatedItems = $items->map(function ($item) use (&$grandTotal) {
          $quantity = (int) $item['quantity'];

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

        $construction = WarehouseConstruction::create([
          'construction_number' => $this->generateNumber(),
          'warehouse_name' => $data['warehouse_name'],
          'latitude' => $data['latitude'],
          'longitude' => $data['longitude'],
          'grand_total_budget' => $grandTotal,
          'status' => 'draft',
        ]);

        $construction->items()->createMany($calculatedItems->toArray());

        $warehouseSlug = Str::slug($data['warehouse_name']);

        foreach ($data['documents'] as $index => $file) {

          $generatedName = sprintf('%s-%d.pdf', $warehouseSlug, $index + 1);

          $filePath = "warehouse-constructions/{$generatedName}";

          Storage::disk('public')->put($filePath, file_get_contents($file->getRealPath()));

          $storedFiles[] = $filePath;

          $construction->documents()->create([
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $generatedName,
            'file_path' => $filePath,
            'file_mime' => $file->getMimeType(),
            'file_size' => Storage::disk('public')->size($filePath),
          ]);
        }

        return $construction;
      });
    } catch (Throwable $e) {
      foreach ($storedFiles as $file) {
        if (Storage::disk('public')->exists($file)) {
          Storage::disk('public')->delete($file);
        }
      }

      throw $e;
    }
  }

  private function generateNumber(): string
  {
    $prefix = 'WC/' . now()->format('ym') . '-';

    $lastNumber = WarehouseConstruction::where('construction_number', 'like', "{$prefix}%")->lockForUpdate()->get()->map(function ($row) {
      preg_match('/(\d+)$/', $row->construction_number, $matches);

      return (int) ($matches[1] ?? 0);
    })->max();

    $nextNumber = ($lastNumber ?? 0) + 1;

    return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
  }
}
