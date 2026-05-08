<?php

namespace App\Domains\Permissions;

use App\Domains\Permissions\Queries\IndexService;
use App\Domains\Permissions\Requests\StoreRequest;
use App\Domains\Permissions\Requests\UpdateRequest;
use App\Domains\Permissions\Services\StoreService;
use App\Domains\Permissions\Services\TerminateService;
use App\Domains\Permissions\Services\UpdateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Throwable;

class PermissionController extends Controller
{
  public function view()
  {
    return view('content.authorization.permissions');
  }

  public function index(Request $request, IndexService $service)
  {
    return response()->json(
      $service->execute([
        'search' => $request->input('search.value'),
        'group' => $request->input('columns.' . $request->input('groupColumn', 3) . '.search.value'),
        'start' => $request->input('start'),
        'length' => $request->input('length'),
        'draw' => $request->input('draw'),
        'getAllPermissions' => $request->boolean('getAllPermissions'),
      ])
    );
  }

  public function store(StoreRequest $request, StoreService $service)
  {
    try {
      $permission = $service->execute($request->validated());

      return response()->json(['status' => 'success', 'message' => "{$permission->group_name}: {$permission->display_name} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating permission', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function edit(Permission $permission)
  {
    return response()->json($permission, 200);
  }

  public function update(UpdateRequest $request, Permission $permission, UpdateService $service)
  {
    try {
      $service->execute($permission, $request->validated());

      return response()->json(['status' => 'success', 'message' => "{$permission->group_name}: {$permission->display_name} updated successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating permission', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(Permission $permission, TerminateService $service)
  {
    try {
      if ($permission->roles()->count() > 0) {
        return response()->json(['status' => 'danger', 'message' => "Permission: {$permission->display_name} cannot be deleted because it is assigned to one or more roles"], 422);
      }

      $service->delete($permission);

      return response()->json(['status' => 'success', 'message' => "Permission deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while deleting permission', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }
}
