<?php

namespace App\Http\Controllers\master;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\OrgUnit as ModelsOrgUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrgUnit extends Controller
{
  public function view()
  {
    return view('content.master.org_units');
  }

  public function select(Request $request)
  {
    $q     = trim((string) $request->get('q', ''));
    $page  = max(1, (int) $request->get('page', 1));
    $per   = max(1, min(100, (int) $request->get('per', 10)));

    $unitType = $request->get('unit_type');

    $query = ModelsOrgUnit::query()->select(['id', 'unit_name']);

    if ($q !== '') {
      $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      foreach ($tokens as $t) {
        $t = str_replace(['%', '_'], ['\\%', '\\_'], $t);
        $query->where('unit_name', 'LIKE', "%{$t}%");
      }
    }

    $query->orderBy('unit_name');

    $rows = $query->skip(($page - 1) * $per)->take($per + 1)->get();

    $more = $rows->count() > $per;
    if ($more) $rows = $rows->slice(0, $per);

    return response()->json([
      'results' => $rows->map(fn($r) => [
        'id'   => $r->id,
        'text' => $r->unit_name,
      ])->values(),
      'more' => $more
    ]);
  }

  public function index(Request $request)
  {
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $parentId = $request->query('parent_id');

    $query = ModelsOrgUnit::query()->orderBy('sort_order');

    if ($isAdmin) {
      $query->withTrashed();
    }

    if ($parentId === null) {
      $query->whereNull('parent_id')->where('unit_type', 'Office');
      $breadcrumbs = [];
    } else {
      $query->where('parent_id', $parentId);

      $path = ModelsOrgUnit::with('parent')->findOrFail($parentId);

      $breadcrumbs = [];
      while ($path) {
        $breadcrumbs[] = ['id' => $path->id, 'name' => $path->unit_name];
        $path = $path->parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
    }

    $units = $query->get(['id', 'unit_name', 'unit_code', 'unit_type', 'deleted_at']);

    return response()->json(['status' => 'success', 'data'   => $units, 'breadcrumbs' => $breadcrumbs], 200);
  }

  public function store(Request $request)
  {
    try {
      $validated = $request->validate([
        'unit_name' => 'required|string|max:100',
        'unit_code' => 'required|string|max:20|unique:org_units,unit_code',
        'unit_type' => 'required|in:Office,Division,Department',
        'parent_id' => 'nullable|exists:org_units,id'
      ]);

      $validated['created_by'] = auth()->user()->id;

      $orgUnit = DB::transaction(function () use ($validated) {
        $parentId = $validated['parent_id'] ?? null;
        $validated['sort_order'] = ModelsOrgUnit::nextSortOrder($parentId);

        return ModelsOrgUnit::create($validated);
      });

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

  public function update(ModelsOrgUnit $orgUnit, Request $request)
  {
    try {
      $validated = $request->validate([
        'unit_name' => 'required|string|max:100',
        'unit_code' => 'required|string|max:20|unique:org_units,unit_code,' . $orgUnit->id,
        'unit_type' => 'required|in:Office,Division,Department',
        'parent_id' => 'nullable|exists:org_units,id'
      ]);

      DB::transaction(function () use ($orgUnit, $validated) {
        if ($orgUnit->parent_id !== ($validated['parent_id'] ?? null)) {
          $orgUnit->parent_id  = $validated['parent_id'] ?? null;
          $orgUnit->sort_order = ModelsOrgUnit::nextSortOrder($orgUnit->parent_id);
        }

        $orgUnit->fill($validated)->save();
      });

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

  public function destroy(ModelsOrgUnit $orgUnit, Request $request)
  {
    try {
      DB::transaction(function () use ($orgUnit) {
        $orgUnit->delete();
      });

      return response()->json(['status' => 'success', 'message' => "Organization Unit: {$orgUnit->unit_name} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function reorder(Request $request)
  {
    try {
      $validated = $request->validate([
        'parent_id'      => ['nullable', 'integer', 'exists:org_units,id'],
        'items'          => ['required', 'array', 'min:1'],
        'items.*.id'     => ['required', 'integer', 'exists:org_units,id'],
      ]);

      $parentId = $validated['parent_id'] ?? null;
      $items    = $validated['items'];

      DB::transaction(function () use ($items, $parentId) {
        foreach ($items as $index => $item) {
          ModelsOrgUnit::where('id', $item['id'])->update([
            'sort_order' => $index + 1,
            'parent_id'  => $parentId,
          ]);
        }
      });

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

  public function restore(string $id)
  {
    $orgUnit = ModelsOrgUnit::withTrashed()->findOrFail($id);

    if (!$orgUnit) {
      return response()->json(['status' => 'danger', 'message' => 'Organization Unit not found'], 404);
    }

    try {
      if ($orgUnit->trashed()) {

        DB::transaction(function () use ($orgUnit) {
          $orgUnit->restore();
        });

        return response()->json(['status' => 'success', 'message' => "Organization Unit: {$orgUnit->unit_name} successfully restored"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function force(string $id)
  {
    $orgUnit = ModelsOrgUnit::withTrashed()->findOrFail($id);

    if (!$orgUnit) {
      return response()->json(['status' => 'danger', 'message' => 'Organization Unit not found'], 404);
    }

    try {
      if ($orgUnit->trashed()) {
        DB::transaction(function () use ($orgUnit) {
          $orgUnit->forceDelete();
        });


        return response()->json(['status' => 'success', 'message' => 'Organization Unit permanent delete successfully'], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }
}
