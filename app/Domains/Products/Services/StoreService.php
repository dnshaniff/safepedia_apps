<?php

namespace App\Domains\Products\Services;

use Throwable;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class StoreService
{
  public function execute(array $data): Product
  {
    $storedFiles = [];

    try {
      return DB::transaction(function () use ($data, &$storedFiles) {

        $product = Product::create([
          'name' => $data['name'],
          'slug' => Str::slug($data['name']) . '-' . Str::ulid(),
          'description' => $data['description'],
          'brand_id' => $data['brand_id'],
          'status' => ucfirst($data['status']),
        ]);

        $manager = ImageManager::usingDriver(Driver::class);

        foreach ($data['images'] as $index => $file) {
          $generatedName = Str::random(40) . '.webp';

          $image = $manager->decode($file)->scaleDown(width: 1200);

          $encoded = $image->encode(new WebpEncoder(quality: 75));

          $filePath = "products/{$generatedName}";

          Storage::disk('public')->put($filePath, (string) $encoded);

          $storedFiles[] = $filePath;

          ProductImage::create([
            'product_id' => $product->id,
            'file_name' => $generatedName,
            'file_path' => $filePath,
            'file_mime' => 'image/webp',
            'file_size' => Storage::disk('public')->size($filePath),
            'is_primary' => $index == $data['thumbnail_index'],
          ]);
        }

        return $product;
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
}
