<?php

namespace App\Http\Controllers\hr;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\OrgUnit as ModelsOrgUnit;
use App\Services\OrgUnit\OrgUnitDestroyService;
use App\Services\OrgUnit\OrgUnitForceService;
use App\Services\OrgUnit\OrgUnitIndexService;
use App\Services\OrgUnit\OrgUnitReorderService;
use App\Services\OrgUnit\OrgUnitRestoreService;
use App\Services\OrgUnit\OrgUnitSelectService;
use App\Services\OrgUnit\OrgUnitStoreService;
use App\Services\OrgUnit\OrgUnitUpdateService;
use Illuminate\Validation\ValidationException;

class OrgUnit extends Controller
{
  public function view()
  {
    return view('content.hr.org_units');
  }

  public function select(Request $request, OrgUnitSelectService $service)
  {
    $search = trim((string) $request->get('q', ''));
    $page = max(1, (int) $request->get('page', 1));

    $perPage = max(1, min(100, (int) $request->get('per', 10)));

    $result = $service->execute($search, $page, $perPage);

    return response()->json($result);
  }

  public function index(Request $request, OrgUnitIndexService $service)
  {
    return response()->json($service->execute($request->query('parent_id')));
  }

  public function store(Request $request, OrgUnitStoreService $service)
  {
    try {
      $validated = $request->validate([
        'unit_name' => 'required|string|max:100',
        'unit_code' => 'required|string|max:20|unique:org_units,unit_code',
        'unit_type' => 'required|in:Office,Division,Department,Team',
        'parent_id' => 'nullable|exists:org_units,id'
      ]);

      $orgUnit = $service->execute($validated);

      return response()->json(['status' => 'success', 'message' => "Organization Unit: {$orgUnit->unit_name} created successfully"], 201);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message, 'errors' => $e->errors()], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function edit(ModelsOrgUnit $orgUnit)
  {
    $orgUnit->load('parent:id,unit_name');

    return response()->json($orgUnit, 200);
  }

  public function update(ModelsOrgUnit $orgUnit, Request $request, OrgUnitUpdateService $service)
  {
    try {
      $validated = $request->validate([
        'unit_name' => 'required|string|max:100',
        'unit_code' => 'required|string|max:20|unique:org_units,unit_code,' . $orgUnit->id,
        'unit_type' => 'required|in:Office,Division,Department,Team',
        'parent_id' => 'nullable|exists:org_units,id'
      ]);

      $service->execute($orgUnit, $validated);

      return response()->json(['status' => 'success', 'message' => "Organization Unit: {$orgUnit->unit_name} updated successfully"], 200);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message, 'errors' => $e->errors()], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occured while processing your request', 'errors' => $e], 500);
    }
  }

  public function destroy(ModelsOrgUnit $orgUnit, OrgUnitDestroyService $service)
  {
    try {
      $service->execute($orgUnit);

      return response()->json(['status' => 'success', 'message' => "Organization Unit: {$orgUnit->unit_name} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function reorder(Request $request, OrgUnitReorderService $service)
  {
    try {
      $validated = $request->validate([
        'parent_id'      => ['nullable', 'integer', 'exists:org_units,id'],
        'items'          => ['required', 'array', 'min:1'],
        'items.*.id'     => ['required', 'integer', 'exists:org_units,id'],
      ]);

      $service->execute($validated['parent_id'] ?? null, $validated['items']);

      return response()->json(['status'  => 'success', 'message' => 'Organization units reordered successfully'], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occured while processing your request', 'errors' => $e], 500);
    }
  }

  public function restore(string $id, OrgUnitRestoreService $service)
  {
    $orgUnit = ModelsOrgUnit::withTrashed()->findOrFail($id);

    if (!$orgUnit) {
      return response()->json(['status' => 'danger', 'message' => 'Organization Unit not found'], 404);
    }

    try {
      if ($orgUnit->trashed()) {
        $service->execute($orgUnit);

        return response()->json(['status' => 'success', 'message' => "Organization Unit: {$orgUnit->unit_name} successfully restored"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function force(string $id, OrgUnitForceService $service)
  {
    $orgUnit = ModelsOrgUnit::withTrashed()->findOrFail($id);

    if (!$orgUnit) {
      return response()->json(['status' => 'danger', 'message' => 'Organization Unit not found'], 404);
    }

    try {
      if ($orgUnit->trashed()) {
        $service->execute($orgUnit);

        return response()->json(['status' => 'success', 'message' => 'Organization Unit permanent delete successfully'], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }
}
