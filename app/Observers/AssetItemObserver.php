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
      $assetItem->public_code = 'AST-' . Str::uuid()->toString();
    }
  }
}
