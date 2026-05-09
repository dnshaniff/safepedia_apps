<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
  use SoftDeletes, Blameable;

  protected $table = 'brands';

  protected $fillable = [
    'name',
    'file_name',
    'file_path',
    'file_mime',
    'file_size',
    'created_by',
    'updated_by',
    'deleted_by'
  ];

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
