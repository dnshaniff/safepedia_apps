<?php

namespace App\Domains\Roles;

use App\Domains\Roles\Queries\IndexService;
use App\Domains\Roles\Queries\SelectService;
use App\Domains\Roles\Requests\StoreRequest;
use App\Domains\Roles\Requests\UpdateRequest;
use App\Domains\Roles\Services\StoreService;
use App\Domains\Roles\Services\TerminateService;
use App\Domains\Roles\Services\UpdateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleController extends Controller
{
  public function view()
  {
    return view('content.authorization.roles');
  }

  public function select(Request $request, SelectService $service)
  {
    $search = trim((string) $request->get('q', ''));
    $page = max(1, (int) $request->get('page', 1));

    $perPage = max(1, min(100, (int) $request->get('per', 10)));

    $result = $service->execute($search, $page, $perPage);

    return response()->json($result);
  }

  public function index(Request $request, IndexService $service)
  {
    return response()->json(
      $service->execute([
        'search' => $request->input('search.value'),
        'start' => $request->input('start'),
        'length' => $request->input('length'),
        'draw' => $request->input('draw'),
      ])
    );
  }

  public function store(StoreRequest $request, StoreService $service)
  {
    try {

      $role = $service->execute($request->validated());

      return response()->json(['status' => 'success', 'message' => "Role: {$role->name} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating role', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function edit(Role $role)
  {
    $permissions = $role->permissions->pluck('name');

    return response()->json(['name' => $role->name, 'permissions' => $permissions], 200);
  }

  public function update(UpdateRequest $request, Role $role, UpdateService $service)
  {
    try {
      $service->execute($role, $request->validated());

      return response()->json(['status' => 'success', 'message' => "Role: {$role->name} updated successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating role', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(Role $role, TerminateService $service)
  {
    try {
      if ($role->users()->count() > 0) {
        return response()->json(['status' => 'danger', 'message' => "Role: {$role->name} cannot be deleted because it is assigned to one or more users"], 422);
      }

      $service->delete($role);

      return response()->json(['status' => 'success', 'message' => "Role deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while deleting role', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }
}
