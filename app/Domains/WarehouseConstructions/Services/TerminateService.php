<?php

namespace App\Domains\WarehouseConstructions\Services;

use App\Models\WarehouseConstruction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TerminateService
{
  public function delete(WarehouseConstruction $warehouseConstruction): void
  {
    if ($warehouseConstruction->status !== 'draft') {
      throw new Exception('Only draft construction can be deleted');
    }

    DB::transaction(function () use ($warehouseConstruction) {
      $warehouseConstruction->delete();
    });
  }

  public function restore(WarehouseConstruction $warehouseConstruction): bool
  {
    if (! $warehouseConstruction->trashed()) {
      return false;
    }

    DB::transaction(function () use ($warehouseConstruction) {
      $warehouseConstruction->restore();
    });

    return true;
  }

  public function force(WarehouseConstruction $warehouseConstruction): bool
  {
    if (! $warehouseConstruction->trashed()) {
      return false;
    }

    DB::transaction(function () use ($warehouseConstruction) {
      $documents = $warehouseConstruction->documents()->get();

      foreach ($documents as $document) {
        if ($document->file_path) {
          Storage::disk('public')->delete($document->file_path);
        }
      }

      $warehouseConstruction->items()->delete();
      $warehouseConstruction->documents()->delete();

      $warehouseConstruction->forceDelete();
    });

    return true;
  }
}
