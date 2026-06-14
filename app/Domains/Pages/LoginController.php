<?php

namespace App\Domains\Pages;

use App\Domains\Pages\Requests\LoginRequest;
use App\Domains\Pages\Services\LoginService;
use App\Domains\Pages\Services\LogoutService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use Throwable;

class LoginController extends Controller
{
  public function view()
  {
    $pageConfigs = ['myLayout' => 'blank'];

    return view('content.pages.login', compact('pageConfigs'));
  }

  public function store(LoginRequest $request, LoginService $service)
  {
    try {
      $requires2FA = $service->execute($request);

      if ($requires2FA) {
        return redirect()->route('twofactor.index');
      }

      return redirect()->intended('/');
    } catch (ValidationException $e) {
      throw $e;
    } catch (Throwable $e) {
      Log::error('Unexpected error while login', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function destroy(Request $request, LogoutService $service)
  {
    $service->execute($request);

    return redirect()->route('login')->with('success', 'You have been successfully logged out');
  }

  public function twofactorView()
  {
    $pageConfigs = ['myLayout' => 'blank'];

    if (! Auth::check()) {
      return redirect()->route('login');
    }

    if (
      session('two_factor_verified')
    ) {
      return redirect('/');
    }

    return view('content.pages.twofactor', compact('pageConfigs'));
  }

  public function twofactorStore(Request $request)
  {
    $request->validate(['otp' => ['required', 'digits:6']]);

    $user = Auth::user();

    if (! $user) {
      Auth::logout();

      return redirect()->route('login.index');
    }

    $google2fa = new Google2FA();

    $valid = $google2fa->verifyKey($user->google2fa_secret, $request->otp, 0);

    if (! $valid) {
      return back()->withErrors(['otp' => 'Invalid verification code']);
    }

    $request->session()->put('two_factor_verified', true);

    return redirect()->intended('/');
  }
}
