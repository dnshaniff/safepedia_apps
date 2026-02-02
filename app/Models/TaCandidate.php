<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaCandidate extends Model
{
  use SoftDeletes;

  protected $table = 'ta_candidates';

  protected $fillable = [
    'manpower_plan_id',
    'full_name',
    'gender',
    'email',
    'phone_number',
    'interview_status',
    'expected_join_date',
    'notes'
  ];

  protected $dates = ['expected_join_date'];

  public function manpowerPlan()
  {
    return $this->belongsTo(ManpowerPlan::class, 'manpower_plan_id', 'id');
  }

  public function newComer()
  {
    return $this->hasOne(NewComer::class, 'ta_candidate_id', 'id');
  }
}
