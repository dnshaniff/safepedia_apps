<?php

namespace App\Observers;

use App\Models\ManpowerPlan;

class ManpowerPlanObserver
{
  public function creating(ManpowerPlan $manpowerPlan)
  {
    if (auth()->check()) {
      $manpowerPlan->created_by = auth()->id();
    }
  }
}
