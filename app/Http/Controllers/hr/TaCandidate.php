<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\Models\ManpowerPlan;
use App\Models\TaCandidate as ModelsTaCandidate;
use Illuminate\Http\Request;

class TaCandidate extends Controller
{
  public function index(ManpowerPlan $manpowerPlan, Request $request)
  {
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $search = $request->input('search.value');

    $query = ModelsTaCandidate::query()->when($isAdmin, function ($q) {
      $q->withTrashed();
    });

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where('full_name', 'LIKE', "%{$search}%");
    }

    $totalFiltered = $query->count();

    $taCandidates = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($taCandidates)) {
      $ids = $request->input('start');
      foreach ($taCandidates as $taCandidate) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $taCandidate->id;
        $nestedData['full_name'] = $taCandidate->full_name;
        $nestedData['gender'] = $taCandidate->gender;
        $nestedData['email'] = $taCandidate->email;
        $nestedData['phone_number'] = $taCandidate->phone_number;
        $nestedData['interview_status'] = $taCandidate->interview_status;
        $nestedData['expected_join_date'] = $taCandidate->expected_join_date?->format('d F Y') ?? '-';
        $nestedData['notes'] = $taCandidate->notes;
        $nestedData['creator'] = $taCandidate->creator?->display_name ?? '-';
        $nestedData['created_at'] = $taCandidate->created_at;
        $nestedData['updated_at'] = $taCandidate->updated_at;
        $nestedData['deleted_at'] = $taCandidate->deleted_at;

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

  public function store(ManpowerPlan $manpowerPlan, Request $request)
  {
    //
  }

  public function edit(ManpowerPlan $manpowerPlan, ModelsTaCandidate $taCandidate)
  {
    //
  }

  public function update(ManpowerPlan $manpowerPlan, ModelsTaCandidate $taCandidate, Request $request)
  {
    //
  }

  public function destroy(ManpowerPlan $manpowerPlan, ModelsTaCandidate $taCandidate)
  {
    //
  }

  public function restore(ManpowerPlan $manpowerPlan, string $id)
  {
    $taCandidate = ModelsTaCandidate::withTrashed()->findOrFail($id);
  }

  public function force(ManpowerPlan $manpowerPlan, string $id)
  {
    $taCandidate = ModelsTaCandidate::withTrashed()->findOrFail($id);
  }
}
