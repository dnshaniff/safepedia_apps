<?php

namespace App\Domains\Brands\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TerminateService
{
  public function delete(Brand $brand): void
  {
    DB::transaction(function () use ($brand) {
      $brand->delete();
    });
  }

  public function restore(Brand $brand): bool
  {
    if (! $brand->trashed()) {
      return false;
    }

    DB::transaction(function () use ($brand) {
      $brand->restore();
    });

    return true;
  }

  public function force(Brand $brand): bool
  {
    if (! $brand->trashed()) {
      return false;
    }

    $filePath = $brand->file_path;

    DB::transaction(function () use ($brand) {
      $brand->forceDelete();
    });

    Storage::disk('public')->delete($filePath);

    return true;
  }
}
