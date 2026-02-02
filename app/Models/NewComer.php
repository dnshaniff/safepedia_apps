<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewComer extends Model
{
  use SoftDeletes;

  protected $table = 'new_comers';

  protected $fillable = [
    'ta_candidate_id',
    'join_date',
    'end_of_contract_date',
    'working_status',
    'manager_id',
    'company_id',
    'email',
    'phone_number',
    'status_join',
    'notes'
  ];

  protected $dates = ['join_date', 'end_of_contract_date'];

  public function taCandidate()
  {
    return $this->belongsTo(TaCandidate::class, 'ta_candidate_id', 'id');
  }
}
