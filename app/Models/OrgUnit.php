<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrgUnit extends Model
{
  use SoftDeletes;

  protected $table = 'org_units';

  protected $fillable = ['parent_id', 'unit_name', 'unit_code', 'unit_type', 'sort_order'];

  public function parent()
  {
    return $this->belongsTo(OrgUnit::class, 'parent_id', 'id');
  }

  public function children()
  {
    return $this->hasMany(OrgUnit::class, 'parent_id', 'id');
  }

  public function childrenRecursive()
  {
    return $this->hasMany(OrgUnit::class, 'parent_id', 'id')->orderBy('sort_order')->with('childrenRecursive');
  }

  public static function nextSortOrder(?int $parentId): int
  {
    $max = static::where('parent_id', $parentId)->max('sort_order');
    return is_null($max) ? 1 : $max + 1;
  }
}
