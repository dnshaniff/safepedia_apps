<?php

namespace App\Domains\Pages;

use App\Domains\Pages\Requests\UpdateProfileRequest;
use App\Domains\Pages\Services\TwoFactorService;
use App\Domains\Pages\Services\UpdateProfileService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProfileController extends Controller
{
  public function view($username)
  {
    $user = User::where('username', $username)->firstOrFail();

    return view('content.pages.profile', compact('user'));
  }

  public function update(UpdateProfileRequest $request, $username, UpdateProfileService $service)
  {
    try {
      $user = User::where('username', $username)->firstOrFail();

      $service->execute($user, $request->validated());

      return response()->json(['status' => 'success', 'message' => 'Profile updated successfully', 'redirect' => route('profile.view', $user->username)], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function generateTwoFactor(string $username, TwoFactorService $service)
  {
    try {
      $user = User::where('username', $username)->firstOrFail();

      $service = $service->execute($user);

      return response()->json(['status' => 'success', 'secret' => $service, 'qr_url' => $service['qr_url'], 'qr_svg' => $service['qr_svg']], 200);
    } catch (Throwable $e) {
      Log::error($e);

      return response()->json(['status' => 'danger', 'message' => 'Failed to generate secret'], 500);
    }
  }
}
