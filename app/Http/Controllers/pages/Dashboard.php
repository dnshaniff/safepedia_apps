<?php

namespace App\Http\Controllers\pages;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Dashboard extends Controller
{
  public function view(Request $request)
  {
    return view('content.pages.dashboard');
  }

  public function profile($username)
  {
    $user = User::where('username', $username)->firstOrFail();
    $employee = $user->employee;

    return view('content.pages.profile', compact('user', 'employee'));
  }

  public function update(UpdateProfileRequest $request, $username)
  {
    $user = User::where('username', $username)->firstOrFail();

    try {
      DB::transaction(function () use ($request, $user) {
        $validated = $request->validated();
        $data = ['username' => $validated['username']];

        if (!empty($validated['password'])) {
          $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        if (!empty($validated['personal_email'])) {
          $user->employee->update(['personal_email' => $validated['personal_email']]);
        }
      });

      return response()->json(['status' => 'success', 'message' => 'Profile updated successfully', 'redirect' => route('profile.view', $user->username)], 200);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message, 'errors' => $e->errors()], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }
}
