<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
  use SoftDeletes, Blameable;

  protected $table = 'invoices';

  protected $fillable = [
    'proforma_number',
    'invoice_number',
    'customer_name',
    'customer_address',
    'customer_phone',
    'reference',
    'payment_terms',
    'issued_date',
    'valid_until',
    'subtotal',
    'discount_total',
    'grand_total',
    'created_by',
    'updated_by',
    'deleted_by'
  ];

  public $casts = [
    'issued_date' => 'date',
    'valid_until' => 'date',
    'subtotal' => 'decimal:2',
    'discount_total' => 'decimal:2',
    'grand_total' => 'decimal:2'
  ];

  public function items()
  {
    return $this->hasMany(InvoiceItem::class);
  }

  public function payments()
  {
    return $this->hasMany(InvoicePayment::class);
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
