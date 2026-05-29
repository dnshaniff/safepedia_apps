<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
  protected $table = 'invoice_items';

  protected $fillable = [
    'invoice_id',
    'product_id',
    'quantity',
    'uom',
    'unit_price',
    'discount',
    'line_total'
  ];

  protected $casts = ['unit_price' => 'decimal:2', 'discount' => 'decimal:2', 'line_total' => 'decimal:2'];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}
