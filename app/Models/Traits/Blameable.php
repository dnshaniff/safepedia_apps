<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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

      if (!Auth::check()) {
        return;
      }

      if (!in_array(SoftDeletes::class, class_uses_recursive($model))) {
        return;
      }

      if (!Schema::hasColumn($model->getTable(), 'deleted_by')) {
        return;
      }

      $model->deleted_by = Auth::id();

      $model->saveQuietly();
    });
  }
}
