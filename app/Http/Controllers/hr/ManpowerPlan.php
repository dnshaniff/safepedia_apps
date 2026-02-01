<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreManpowerPlanRequest;
use App\Http\Requests\UpdateManpowerPlanRequest;
use App\Models\ManpowerPlan as ModelsManpowerPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Throw_;
use Throwable;

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
        $nestedData['devices'] = $manpowerPlan->devices->pluck('type_name')->join(', ');
        $nestedData['status'] = 'Pending';
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

  public function store(StoreManpowerPlanRequest $request)
  {
    try {
      $manpowerPlan = DB::transaction(function () use ($request) {
        $data = $request->validated();
        $devices = $data['devices'];
        unset($data['devices']);

        $manpowerPlan = ModelsManpowerPlan::create($data);
        $manpowerPlan->devices()->sync($devices);
      });

      return response()->json(['status' => 'success', 'message' => "Plan: {$manpowerPlan->position_title} created successfully", 201]);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occured while processing your request', 'errors' => $e], 500);
    }
  }

  public function show(ModelsManpowerPlan $manpowerPlan)
  {
    $manpowerPlan->load([
      'orgUnit:id,unit_name',
      'devices:id,type_name',
      'creator.employee:id,user_id,full_name'
    ]);

    return view('content.hr.manpower_plans-show', compact('manpowerPlan'));
  }

  public function edit(ModelsManpowerPlan $manpowerPlan)
  {
    $manpowerPlan->load(['orgUnit:id,unit_name', 'devices:id,type_name']);

    return response()->json($manpowerPlan, 200);
  }

  public function update(UpdateManpowerPlanRequest $request, ModelsManpowerPlan $manpowerPlan)
  {
    try {
      DB::transaction(function () use ($request, $manpowerPlan) {
        $data = $request->validated();
        $devices = $data['devices'];
        unset($data['devices']);

        $manpowerPlan->update($data);
        $manpowerPlan->devices()->sync($devices);
      });

      return response()->json(['status' => 'success', 'message' => "Plan: {$manpowerPlan->position_title} updated successfully", 200]);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occured while processing your request', 'errors' => $e], 500);
    }
  }

  public function destroy(ModelsManpowerPlan $manpowerPlan)
  {
    try {
      $manpowerPlan->delete();

      return response()->json(['status' => 'success', 'message' => "Plan: {$manpowerPlan->position_title} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occured while processing your request'], 500);
    }
  }

  public function restore(string $id)
  {
    $manpowerPlan = ModelsManpowerPlan::withTrashed()->findOrFail($id);

    try {
      if ($manpowerPlan->trashed()) {
        $manpowerPlan->restore();

        return response()->json(['status' => 'success', 'message' => "Plan: {$manpowerPlan->position_title} restored successfully"], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occured while processing your request'], 500);
    }
  }

  public function force(string $id)
  {
    $manpowerPlan = ModelsManpowerPlan::withTrashed()->findOrFail($id);

    try {
      if ($manpowerPlan->trashed()) {
        $manpowerPlan->forceDelete();

        return response()->json(['status' => 'success', 'message' => 'Plan permanent delete successfully'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occured while processing your request'], 500);
    }
  }
}
