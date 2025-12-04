<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTitle extends Model
{
  use SoftDeletes;

  protected $table = 'job_titles';

  protected $fillable = ['title_name'];
}
