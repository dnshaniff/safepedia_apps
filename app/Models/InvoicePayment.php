<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
  use Blameable;

  protected $table = 'invoice_payments';

  protected $fillable = [
    'invoice_id',
    'payment_date',
    'amount',
    'payment_method',
    'notes',
    'file_name',
    'file_path',
    'file_mime',
    'file_size',
    'created_by',
    'updated_by'
  ];

  protected $casts = ['amount' => 'decimal:2'];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function editor()
  {
    return $this->belongsTo(User::class, 'updated_by', 'id');
  }
}
