<?php

namespace App\Domains\Users;

use App\Domains\Users\Queries\IndexService;
use App\Domains\Users\Requests\StoreRequest;
use App\Domains\Users\Requests\UpdateRequest;
use App\Domains\Users\Services\StoreService;
use App\Domains\Users\Services\TerminateService;
use App\Domains\Users\Services\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
  public function view()
  {
    return view('content.authorization.users');
  }

  public function index(Request $request, IndexService $service)
  {
    return response()->json(
      $service->execute([
        'search' => $request->input('search.value'),
        'role' => $request->input('columns.2.search.value'),
        'start' => $request->input('start'),
        'length' => $request->input('length'),
        'draw' => $request->input('draw'),
      ])
    );
  }

  public function store(StoreRequest $request, StoreService $service)
  {
    try {
      $user = $service->execute($request->validated());

      return response()->json(['status' => 'success', 'message' => "User {$user->name} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating user', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function edit(User $user)
  {
    $user->load('roles');

    return response()->json($user, 200);
  }

  public function update(UpdateRequest $request, User $user, UpdateService $service)
  {
    try {
      $service->execute($user, $request->validated());

      return response()->json(['status' => 'success', 'message' => "User {$user->name} updated successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating user', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(User $user, TerminateService $service)
  {
    try {
      $service->delete($user);

      return response()->json(['status' => 'success', 'message' => "User: {$user->username} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function restore(string $id, TerminateService $service)
  {
    $user = User::withTrashed()->findOrFail($id);

    try {
      if ($user->trashed()) {
        $service->restore($user);

        return response()->json(['status' => 'success', 'message' => "User: {$user->username} successfully restored"], 200);
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

  public function force(string $id, TerminateService $service)
  {
    $user = User::withTrashed()->findOrFail($id);

    if ($user->employee()->exists()) {
      return response()->json(['status' => 'info', 'message' => 'Data cannot be deleted because it is associated with other records'], 422);
    }

    try {
      if ($user->trashed()) {
        if ($user->username === 'administrator') {
          return response()->json(['status' => 'danger', 'title' => 'Deletion Prevented', 'message' => "Data cannot be deleted because this user administrator"], 422);
        } else {
          $service->force($user);
        }

        return response()->json(['status' => 'success', 'message' => 'User permanent delete successfully'], 200);
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
