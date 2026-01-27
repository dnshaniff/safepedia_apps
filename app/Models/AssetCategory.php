<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{
  use SoftDeletes;

  protected $table = 'asset_categories';

  protected $fillable = ['category_name', 'category_code', 'created_by'];

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
}
