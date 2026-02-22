<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetItem extends Model
{
  use HasUuids, SoftDeletes;

  protected $table = 'asset_items';

  protected $fillable = [
    'asset_type_id',
    'item_code',
    'item_brand',
    'serial_number',
    'item_model',
    'item_specification',
    'company_id',
    'item_status',
    'created_by'
  ];

  public function type()
  {
    return $this->belongsTo(AssetType::class, 'asset_type_id', 'id');
  }

  public function company()
  {
    return $this->belongsTo(Company::class, 'company_id', 'id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public const STATUSES = [
    'Active',
    'In Repair',
    'Lost',
    'Disposed',
  ];
}
