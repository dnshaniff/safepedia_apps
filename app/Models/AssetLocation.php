<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetLocation extends Model
{
  use SoftDeletes;

  protected $table = 'asset_locations';

  protected $fillable = ['location_name', 'created_by'];

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
}
