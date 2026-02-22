<?php

namespace App\Observers;

use App\Models\AssetItem;
use Illuminate\Support\Str;

class AssetItemObserver
{
  public function creating(AssetItem $assetItem)
  {
    if (auth()->check()) {
      $assetItem->created_by = auth()->id();

      do {
        $code = 'AST-' . Str::upper(Str::random(8));
      } while (AssetItem::where('public_code', $code)->exists());
    }
  }
}
