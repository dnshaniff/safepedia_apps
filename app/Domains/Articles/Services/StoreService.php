<?php

namespace App\Domains\Articles\Services;

use App\Models\Article;
use App\Models\ArticleImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Throwable;

class StoreService
{
  public function execute(array $data): Article
  {
    $storedFiles = [];

    try {
      return DB::transaction(function () use ($data, &$storedFiles) {

        $article = Article::create([
          'title' => $data['title'],
          'slug' => Str::slug($data['title']) . '-' . Str::ulid(),
          'content' => $data['content'],
          'project_at' => $data['project_at'],
          'location' => $data['location'],
          'status' => ucfirst($data['status']),
        ]);

        $manager = ImageManager::usingDriver(Driver::class);

        foreach ($data['images'] as $index => $file) {
          $generatedName = Str::random(40) . '.webp';

          $image = $manager->decode($file)->scaleDown(width: 1200);

          $encoded = $image->encode(new WebpEncoder(quality: 75));

          $filePath = "articles/{$generatedName}";

          Storage::disk('public')->put($filePath, (string) $encoded);

          $storedFiles[] = $filePath;

          ArticleImage::create([
            'article_id' => $article->id,
            'file_name' => $generatedName,
            'file_path' => $filePath,
            'file_mime' => 'image/webp',
            'file_size' => Storage::disk('public')->size($filePath),
            'is_primary' => $index == $data['thumbnail_index'],
          ]);
        }

        return $article;
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
