<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\Models\ManpowerPlan as ModelsManpowerPlan;
use Illuminate\Http\Request;

class ManpowerPlan extends Controller
{
  public function view()
  {
    return view('content.hr.manpower_plans');
  }

  public function index(Request $request)
  {
    //
  }

  public function store(Request $request)
  {
    //
  }

  public function show(ModelsManpowerPlan $manpowerPlan)
  {
    //
  }

  public function edit(ModelsManpowerPlan $manpowerPlan)
  {
    //
  }

  public function update(Request $request, ModelsManpowerPlan $manpowerPlan)
  {
    //
  }

  public function destroy(ModelsManpowerPlan $manpowerPlan)
  {
    //
  }

  public function restore(string $id)
  {
    $manpowerPlan = ModelsManpowerPlan::withTrashed()->findOrFail($id);
  }

  public function force(string $id)
  {
    $manpowerPlan = ModelsManpowerPlan::withTrashed()->findOrFail($id);
  }
}
