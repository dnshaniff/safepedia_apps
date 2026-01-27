<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetType extends Model
{
  use SoftDeletes;

  protected $table = 'asset_types';

  protected $fillable = ['asset_category_id', 'type_name', 'type_code', 'created_by'];

  public function category()
  {
    return $this->belongsTo(AssetCategory::class, 'asset_category_id', 'id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
}
