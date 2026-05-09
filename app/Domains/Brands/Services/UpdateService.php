<?php

namespace App\Domains\Brands\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class UpdateService
{
  public function execute(Brand $brand, array $data): Brand
  {
    $newFilePath = null;
    $oldFilePath = $brand->file_path;
    $isSuccess = false;

    try {
      $updateBrand = DB::transaction(function () use ($brand, $data, &$newFilePath) {
        $payload = ['name' => $data['name']];

        if (!empty($data['file_upload'])) {
          $file = $data['file_upload'];
          $generatedName = Str::slug($data['name']) . '-' . time();

          $extension = $file->getClientOriginalExtension();
          $storedFileName = "{$generatedName}.{$extension}";

          $newFilePath = $file->storeAs('brands', $storedFileName, 'public');

          $payload = array_merge($payload, [
            'file_name' => $storedFileName,
            'file_path' => $newFilePath,
            'file_mime' => $file->getMimeType(),
            'file_size' => $file->getSize(),
          ]);
        }

        $brand->update($payload);

        return $brand->fresh();
      });

      $isSuccess = true;

      if ($newFilePath &&  $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
        Storage::disk('public')->delete($oldFilePath);
      }

      return $updateBrand;
    } catch (Throwable $e) {
      if (!$isSuccess && $newFilePath && Storage::disk('public')->exists($newFilePath)) {
        Storage::disk('public')->delete($newFilePath);
      }

      throw $e;
    }
  }
}
