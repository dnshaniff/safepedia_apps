<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait Blameable
{
  public static function bootBlameable(): void
  {
    static::creating(function ($model) {

      if (! Auth::check()) {
        return;
      }

      if (empty($model->created_by)) {
        $model->created_by = Auth::id();
      }

      if (empty($model->updated_by)) {
        $model->updated_by = Auth::id();
      }
    });

    static::updating(function ($model) {

      if (! Auth::check()) {
        return;
      }

      $model->updated_by = Auth::id();
    });

    static::deleting(function ($model) {

      if (! Auth::check()) {
        return;
      }

      $model->deleted_by = Auth::id();

      $model->saveQuietly();
    });
  }
}
