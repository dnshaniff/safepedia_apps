<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleImage extends Model
{
  protected $table = 'article_images';

  protected $fillable = [
    'article_id',
    'file_name',
    'file_path',
    'file_mime',
    'file_size',
    'is_primary'
  ];

  protected $casts = ['is_primary' => 'boolean'];

  public function article()
  {
    return $this->belongsTo(Article::class);
  }
}
