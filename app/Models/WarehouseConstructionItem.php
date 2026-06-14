<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseConstructionItem extends Model
{
  protected $table = 'warehouse_construction_items';

  protected $fillable = [
    'warehouse_construction_id',
    'item_name',
    'quantity',
    'unit_price',
    'line_total'
  ];

  public function warehouseConstruction()
  {
    return $this->belongsTo(WarehouseConstruction::class, 'warehouse_construction_id', 'id');
  }
}
