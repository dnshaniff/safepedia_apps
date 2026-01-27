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
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $search = $request->input('search.value');

    $query = ModelsManpowerPlan::query()->when($isAdmin, function ($q) {
      $q->withTrashed();
    });

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where('position_title', 'LIKE', "%{$search}%");
    }

    $totalFiltered = $query->count();

    $manpowerPlans = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($manpowerPlans)) {
      $ids = $request->input('start');
      foreach ($manpowerPlans as $manpowerPlan) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $manpowerPlan->id;
        $nestedData['org_unit'] = $manpowerPlan->orgUnit?->unit_name ?? '-';
        $nestedData['position_title'] = $manpowerPlan->position_title;
        $nestedData['planned_date'] = $manpowerPlan->planned_date->format('d F Y');
        $nestedData['number_positions'] = $manpowerPlan->number_positions;
        $nestedData['notes'] = $manpowerPlan->notes;
        $nestedData['devices'] = $manpowerPlan->devices->pluck('type_name')->join(',');
        $nestedData['creator'] = $manpowerPlan->creator?->display_name ?? '-';
        $nestedData['created_at'] = $manpowerPlan->created_at;
        $nestedData['updated_at'] = $manpowerPlan->updated_at;
        $nestedData['deleted_at'] = $manpowerPlan->deleted_at;

        $data[] = $nestedData;
      }
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'code' => 200,
      'data' => $data,
    ]);
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
