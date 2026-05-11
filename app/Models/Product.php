<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
  use SoftDeletes, Blameable;

  protected $table = 'products';

  protected $fillable = [
    'name',
    'slug',
    'description',
    'brand_id',
    'status',
    'created_by',
    'updated_by',
    'deleted_by'
  ];

  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }

  public function thumbnail()
  {
    return $this->hasOne(ProductImage::class)->where('is_primary', true);
  }

  public function images()
  {
    return $this->hasMany(ProductImage::class);
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
