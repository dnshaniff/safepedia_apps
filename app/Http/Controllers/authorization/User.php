<?php

namespace App\Http\Controllers\authorization;

use Throwable;
use Illuminate\Http\Request;
use App\Models\User as ModelsUser;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class User extends Controller
{
  public function view()
  {
    return view('content.authorization.users');
  }

  public function index(Request $request)
  {
    $search = $request->input('search.value');
    $roleFilter = $request->input('columns.2.search.value');

    $query = ModelsUser::withTrashed()->with('roles');

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('username', 'LIKE', "%{$search}%");
      });
    }
    // if (!empty($search)) {
    //   $query->where(function ($q) use ($search) {
    //     $q->where('username', 'LIKE', "%{$search}%")
    //       ->orWhereHas('employee', function ($q) use ($search) {
    //         $q->where('full_name', 'LIKE', "%{$search}%");
    //       });
    //   });
    // }

    if (!empty($roleFilter)) {
      $query->whereHas('roles', function ($q) use ($roleFilter) {
        $q->where('name', $roleFilter);
      });
    }

    $totalFiltered = $query->count();

    $users = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($users)) {
      $ids = $request->input('start');
      foreach ($users as $user) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $user->id;
        $nestedData['username'] = $user->username;
        $nestedData['role'] = $user->roles->pluck('name')->first();
        $nestedData['status'] = $user->status;
        $nestedData['created_at'] = $user->created_at;
        $nestedData['updated_at'] = $user->updated_at;
        $nestedData['deleted_at'] = $user->deleted_at;

        $data[] = $nestedData;
      }
    }

    $roles = Role::select('name')->distinct()->orderBy('name')->pluck('name');

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'code' => 200,
      'data' => $data,
      'roles' => $roles,
    ]);
  }

  public function edit(ModelsUser $user)
  {
    $user->load('roles');

    return response()->json($user);
  }

  public function update(Request $request, ModelsUser $user)
  {
    try {
      $validated = $request->validate([
        'username' => 'required|min:6|max:50|regex:/^[a-z0-9]+$/|unique:users,id,' . $user->id,
        'role' => 'required',
        'status' => 'required'
      ]);

      if ($request->filled('password') || $request->filled('password_confirmation')) {
        $passwordValidate = $request->validate([
          'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/',
          'password_confirmation' => 'required|same:password',
        ]);

        $validated = array_merge($validated, $passwordValidate);
      }

      $user->update([
        'username' => $validated['username'],
        'password' => isset($validated['password']) ? $validated['password'] : $user->password,
        'status' => $validated['status']
      ]);

      // hapus role dari user
      $user->roles()->detach();

      // assign role ke user
      $user->assignRole($validated['role']);

      return response()->json(
        ['status' => 'success', 'message' => 'User: ' . $user->username . ' updated successfully'],
        200
      );
    } catch (ValidationException $e) {
      return response()->json(['status' => 'danger', 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function destroy(ModelsUser $user)
  {
    try {
      $user->delete();
      return response()->json(
        ['status' => 'success', 'message' => $user->username . ' deleted successfully'],
        200
      );
    } catch (Throwable $e) {
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function restore($id)
  {
    $user = ModelsUser::withTrashed()->where('id', $id)->first();

    if (!$user) {
      return response()->json(['status' => 'danger', 'message' => 'User not found'], 404);
    }

    try {
      if ($user->trashed()) {
        $user->restore();

        return response()->json(
          ['status' => 'success', 'message' => $user->username . ' successfully restored'],
          200
        );
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function force($id)
  {
    $user = ModelsUser::withTrashed()->where('id', $id)->first();

    if (!$user) {
      return response()->json(['status' => 'danger', 'message' => 'User not found'], 404);
    }

    try {
      if ($user->employee()->exists()) {
        return response()->json(['status' => 'info', 'message' => 'Data cannot be deleted because it is associated with other records'], 422);
      }

      if ($user->trashed()) {
        if ($user->username === 'administrator') {
          return response()->json([
            'status' => 'danger',
            'title' => 'Deletion Prevented',
            'message' => "Data cannot be deleted because this user administrator"
          ], 422);
        } else {
          $user->forceDelete();
        }

        return response()->json(
          ['status' => 'success', 'message' => 'User permanent delete successfully'],
          200
        );
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }
}
