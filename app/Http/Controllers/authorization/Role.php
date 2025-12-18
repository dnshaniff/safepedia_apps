<?php

namespace App\Http\Controllers\authorization;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends Controller
{
  public function view()
  {
    return view('content.authorization.roles');
  }

  public function select(Request $request)
  {
    $q     = trim((string) $request->get('q', ''));
    $page  = max(1, (int) $request->get('page', 1));
    $per   = max(1, min(100, (int) $request->get('per', 10)));

    $query = ModelsRole::query()->select(['id', 'name']);

    if ($q !== '') {
      $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      foreach ($tokens as $t) {
        $t = str_replace(['%', '_'], ['\\%', '\\_'], $t);
        $query->where('name', 'LIKE', "%{$t}%");
      }
    }

    $query->orderBy('name');

    $rows = $query->skip(($page - 1) * $per)->take($per + 1)->get();

    $more = $rows->count() > $per;
    if ($more) $rows = $rows->slice(0, $per);

    return response()->json([
      'results' => $rows->map(fn($r) => [
        'id'   => $r->name,
        'text' => $r->name,
      ])->values(),
      'more' => $more
    ]);
  }

  public function index(Request $request)
  {
    $search = $request->input('search.value');

    $query = ModelsRole::query();

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('name', 'LIKE', "%{$search}%");
      });
    }

    $totalFiltered = $query->count();

    $start = $request->input('start', 0);
    $length = $request->input('length', 10);

    $roles = $query->offset($start)->limit($length)->latest()->get();

    $data = [];

    if (!empty($roles)) {
      foreach ($roles as $role) {
        $nestedData['fake_id'] = ++$start;
        $nestedData['id'] = $role->id;
        $nestedData['name'] = $role->name;
        $nestedData['created_at'] = $role->created_at;
        $nestedData['updated_at'] = $role->updated_at;

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
    try {
      $validated = $request->validate([
        'name' => 'required|unique:roles,name',
        'permissions.*' => 'required',
      ]);

      $role = DB::transaction(function () use ($validated) {
        return ModelsRole::create(['name' => $validated['name']]);
      });

      $role->syncPermissions($validated['permissions']);

      return response()->json(['status' => 'success', 'message' => "{$role->name} created successfully"], 201);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function edit(ModelsRole $role)
  {
    $permissions = $role->permissions->pluck('name');
    return response()->json(['name' => $role->name, 'permissions' => $permissions], 200);
  }

  public function update(Request $request, ModelsRole $role)
  {
    try {
      $validated = $request->validate([
        'name' => 'required|unique:roles,name,' . $role->id,
        'permissions.*' => 'required|exists:permissions,name',
      ]);

      DB::transaction(function () use ($role, $validated) {
        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions']);
      });

      return response()->json(['status' => 'success', 'message' => "{$role->name} updated successfully"], 200);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function destroy(ModelsRole $role)
  {
    try {
      if ($role->users()->count() > 0) {
        return response()->json(['status' => 'error', 'message' => "Cannot delete role '{$role->name}' because it is associated with users or permissions"], 422);
      }

      $role->delete();

      return response()->json(['status' => 'success', 'message' => "Role deleted successfully"], 200);
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
