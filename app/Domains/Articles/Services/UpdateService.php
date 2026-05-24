<?php

namespace App\Domains\Articles\Services;

use Throwable;
use App\Models\Article;
use App\Models\ArticleImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class UpdateService
{
  public function execute(Article $article, array $data): Article
  {
    $newUploadedPaths = [];
    $deletedPaths = [];

    try {
      $updatedProduct = DB::transaction(function () use ($article, $data, &$newUploadedPaths) {
        $slug = $article->slug;

        if ($article->title !== $data['title']) {
          $slug = Str::slug($data['title']) . '-' . Str::ulid();
        }

        $article->update([
          'title' => $data['title'],
          'slug' => $slug,
          'content' => $data['content'],
          'project_at' => $data['project_at'],
          'location' => $data['location'],
          'status' => $data['status'],
        ]);

        if (!empty($data['removed_images'])) {
          $imagesToDelete = ArticleImage::whereIn('id', $data['removed_images'])->get();

          foreach ($imagesToDelete as $image) {
            Storage::disk('public')->delete($image->file_path);

            $image->delete();
          }
        }

        $manager = ImageManager::usingDriver(Driver::class);

        if (!empty($data['images'])) {
          foreach ($data['images'] as $file) {
            $generatedName = Str::random(40) . '.webp';

            $image = $manager->decode($file)->scaleDown(width: 1200);

            $encoded = $image->encode(new WebpEncoder(quality: 75));

            $filePath = "products/{$generatedName}";

            Storage::disk('public')->put($filePath, (string) $encoded);

            $newUploadedPaths[] = $filePath;

            $image = $article->images()->create([
              'file_name' => $generatedName,
              'file_path' => $filePath,
              'file_mime' => 'image/webp',
              'file_size' => Storage::disk('public')->size($filePath),
              'is_primary' => false,
            ]);

            $newImages[] = $image;
          }
        }

        $article->images()->update(['is_primary' => false]);

        $thumbnailIndex = $data['thumbnail_index'] ?? 0;

        $allImages = $article->images()->get()->values();

        if (isset($allImages[$thumbnailIndex])) {
          $allImages[$thumbnailIndex]->update(['is_primary' => true]);
        }

        return $article->fresh(['images']);
      });

      foreach ($deletedPaths as $path) {
        if (Storage::disk('public')->exists($path)) {
          Storage::disk('public')->delete($path);
        }
      }

      return $updatedProduct;
    } catch (Throwable $e) {
      foreach ($newUploadedPaths as $path) {
        if (Storage::disk('public')->exists($path)) {
          Storage::disk('public')->delete($path);
        }
      }

      throw $e;
    }
  }
}
