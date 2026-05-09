<?php

namespace App\Domains\Brands\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class StoreService
{
  public function execute(array $data): Brand
  {
    $filePath = null;

    try {
      return DB::transaction(function () use ($data, &$filePath) {
        $file = $data['file_upload'] ?? null;

        $generatedName = Str::slug($data['name']) . '-' . time();
        $extension = $file->getClientOriginalExtension();
        $storedFileName = "{$generatedName}.{$extension}";

        $filePath = $file->storeAs('brands', $storedFileName, 'public');

        $brand = Brand::create([
          'name' => $data['name'],
          'file_name' => "{$generatedName}.{$extension}",
          'file_path' => $filePath,
          'file_mime' => $file->getMimeType(),
          'file_size' => $file->getSize(),
        ]);

        return $brand;
      });
    } catch (Throwable $e) {
      if ($filePath && Storage::disk('public')->exists($filePath)) {
        Storage::disk('public')->delete($filePath);
      }

      throw $e;
    }
  }
}
