<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseConstruction extends Model
{
  use SoftDeletes, Blameable;

  protected $table = 'warehouse_constructions';

  protected $fillable = [
    'construction_number',
    'warehouse_name',
    'latitude',
    'longitude',
    'grand_total_budget',
    'status',
    'created_by',
    'updated_by',
    'deleted_by'
  ];

  public function items()
  {
    return $this->hasMany(WarehouseConstructionItem::class, 'warehouse_construction_id', 'id');
  }

  public function documents()
  {
    return $this->hasMany(WarehouseConstructionDocument::class, 'warehouse_construction_id', 'id');
  }

  public function approvals()
  {
    return $this->hasMany(WarehouseConstructionApproval::class, 'warehouse_construction_id', 'id');
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
