<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseConstructionDocument extends Model
{
  protected $table = 'warehouse_construction_documents';

  protected $fillable = [
    'warehouse_construction_id',
    'file_name',
    'file_path',
    'file_mime',
    'file_size',
    'original_name'
  ];

  public function warehouseConstruction()
  {
    return $this->belongsTo(WarehouseConstruction::class, 'warehouse_construction_id', 'id');
  }
}
