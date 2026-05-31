<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
  use SoftDeletes, Blameable;

  protected $table = 'articles';

  protected $fillable = [
    'title',
    'slug',
    'content',
    'status',
    'project_at',
    'location',
    'published_at',
    'created_by',
    'updated_by',
    'deleted_by'
  ];

  protected $casts = ['project_at' => 'date', 'published_at' => 'date'];

  public function thumbnail()
  {
    return $this->hasOne(ArticleImage::class)->where('is_primary', true);
  }

  public function images()
  {
    return $this->hasMany(ArticleImage::class);
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
