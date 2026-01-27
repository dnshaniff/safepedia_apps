<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManpowerPlan extends Model
{
  use HasUuids, SoftDeletes;

  protected $table = 'manpower_plans';

  protected $fillable = [
    'org_unit_id',
    'position_title',
    'planned_date',
    'number_positions',
    'notes',
    'created_by'
  ];

  public function orgUnit()
  {
    return $this->belongsTo(OrgUnit::class, 'org_unit_id', 'id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function devices()
  {
    return $this->belongsToMany(AssetType::class, 'manpower_plan_asset_type', 'manpower_plan_id', 'asset_type_id');
  }

  protected $casts = ['planned_date' => 'date'];
}
