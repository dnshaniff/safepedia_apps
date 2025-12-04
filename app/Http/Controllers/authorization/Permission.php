<?php

namespace App\Http\Controllers\authorization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission as ModelsPermission;
use Throwable;

class Permission extends Controller
{
  public function view()
  {
    return view('content.authorization.permissions');
  }

  public function index(Request $request)
  {
    $search = $request->input('search.value');
    $groupColumnIndex = $request->input('groupColumn', 3);
    $groupFilter = $request->input("columns.{$groupColumnIndex}.search.value");

    $query = ModelsPermission::query();

    if ($request->has('getAllPermissions')) {
      $allPermissions = ModelsPermission::pluck('name')->toArray();
      return response()->json(['allPermissions' => $allPermissions]);
    }

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('name', 'LIKE', "%{$search}%")
          ->orWhere('display_name', 'LIKE', "%{$search}%");
      });
    }

    if (!empty($groupFilter)) {
      $query->where('group_name', $groupFilter);
    }

    $totalFiltered = $query->count();

    $start = $request->input('start', 0);
    $length = $request->input('length', 10);

    $permissions = $query->offset($start)->limit($length)->latest()->get();

    $data = [];

    if (!empty($permissions)) {
      foreach ($permissions as $permission) {
        $nestedData['fake_id'] = ++$start;
        $nestedData['id'] = $permission->id;
        $nestedData['display_name'] = $permission->display_name;
        $nestedData['name'] = $permission->name;
        $nestedData['group_name'] = $permission->group_name;
        $nestedData['created_at'] = $permission->created_at;
        $nestedData['updated_at'] = $permission->updated_at;

        $data[] = $nestedData;
      }
    }

    $groups = ModelsPermission::select('group_name')->distinct()->orderBy('group_name')->pluck('group_name');

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'code' => 200,
      'data' => $data,
      'groups' => $groups,
    ]);
  }

  public function store(Request $request)
  {
    try {
      $validate = $request->validate([
        'display_name' => 'required|min:3',
        'name' => 'required|min:4|unique:permissions,name',
        'group_name' => 'required|min:4',
      ]);

      $permission = ModelsPermission::create($validate);

      return response()->json(['status' => 'success', 'message' => "$permission->group_name: $permission->display_name created successfully"], 201);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message], 422);
    } catch (Throwable $e) {
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function edit(ModelsPermission $permission)
  {
    return response()->json($permission);
  }

  public function update(Request $request, ModelsPermission $permission)
  {
    if (!$permission) {
      return response()->json(['status' => 'danger', 'message' => 'Permission not found'], 404);
    }

    try {
      $validate = $request->validate([
        'display_name' => 'required|min:4',
        'name' => 'required|min:4|unique:permissions,name,' . $permission->id,
        'group_name' => 'required|min:4',
      ]);
      $permission->update($validate);

      return response()->json(['status' => 'success', 'message' => "$permission->group_name: $permission->display_name updated successfully"], 200);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message], 422);
    } catch (Throwable $e) {
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function destroy(ModelsPermission $permission)
  {
    if (!$permission) {
      return response()->json(['status' => 'danger', 'message' => 'Permission not found'], 404);
    }

    try {
      if ($permission->roles()->count() > 0) {
        return response()->json([
          'status' => 'error',
          'message' => "Cannot delete permission '{$permission->name}' because it is associated with roles"
        ], 422);
      }

      $permission->delete();
      return response()->json(['status' => 'success', 'message' => "Permission deleted successfully"], 200);
    } catch (Throwable $e) {
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }
}
