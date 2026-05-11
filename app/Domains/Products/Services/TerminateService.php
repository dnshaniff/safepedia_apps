<?php

namespace App\Domains\Products\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TerminateService
{
  public function delete(Product $product): void
  {
    DB::transaction(function () use ($product) {
      $product->delete();
    });
  }

  public function restore(Product $product): bool
  {
    if (! $product->trashed()) {
      return false;
    }

    DB::transaction(function () use ($product) {
      $product->restore();
    });

    return true;
  }

  public function force(Product $product): bool
  {
    if (! $product->trashed()) {
      return false;
    }

    $imagePaths = $product->images->pluck('file_path')->toArray();

    DB::transaction(function () use ($product) {
      $product->images()->forceDelete();

      $product->forceDelete();
    });

    foreach ($imagePaths as $path) {
      if (Storage::disk('public')->exists($path)) {

        Storage::disk('public')->delete($path);
      }
    }

    return true;
  }
}
