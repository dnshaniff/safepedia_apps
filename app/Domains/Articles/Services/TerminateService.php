<?php

namespace App\Domains\Articles\Services;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TerminateService

{
  public function delete(Article $article): void
  {
    DB::transaction(function () use ($article) {
      $article->delete();
    });
  }

  public function restore(Article $article): bool
  {
    if (! $article->trashed()) {
      return false;
    }

    DB::transaction(function () use ($article) {
      $article->restore();
    });

    return true;
  }

  public function force(Article $article): bool
  {
    if (! $article->trashed()) {
      return false;
    }

    $imagePaths = $article->images->pluck('file_path')->toArray();

    DB::transaction(function () use ($article) {
      $article->images()->forceDelete();

      $article->forceDelete();
    });

    foreach ($imagePaths as $path) {
      if (Storage::disk('public')->exists($path)) {

        Storage::disk('public')->delete($path);
      }
    }

    return true;
  }
}
