<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseConstructionApproval extends Model
{
  protected $table = 'warehouse_construction_approvals';

  protected $fillable = [
    'warehouse_construction_id',
    'approval_id',
    'employee_id',
    'action',
    'notes'
  ];

  public function warehouseConstruction()
  {
    return $this->belongsTo(WarehouseConstruction::class, 'warehouse_construction_id', 'id');
  }

  public function employee()
  {
    return $this->belongsTo(Employee::class, 'employee_id', 'id');
  }

  public function approval()
  {
    return $this->belongsTo(Approval::class, 'approval_id', 'id');
  }
}
