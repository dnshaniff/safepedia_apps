<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrgUnit extends Model
{
  use SoftDeletes, Blameable;

  protected $table = 'org_units';

  protected $fillable = ['parent_id', 'unit_name', 'unit_code', 'unit_type', 'sort_order', 'created_by', 'updated_by', 'deleted_by'];

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

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function editor()
  {
    return $this->belongsTo(User::class, 'updated_by', 'id');
  }

  public function deleter()
  {
    return $this->belongsTo(User::class, 'deleted_by', 'id');
  }
}
