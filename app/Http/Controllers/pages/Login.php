<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];

    return view('content.pages.login', ['pageConfigs' => $pageConfigs]);
  }

  public function store(Request $request)
  {
    $credentials = $request->validate([
      'username' => 'required',
      'password' => 'required',
    ]);

    $this->ensureIsNotRateLimited($request);

    $remember = (bool) $request->boolean('remember');

    if (! Auth::attempt($credentials, $remember)) {
      RateLimiter::hit($this->throttleKey($request), 300);
      throw ValidationException::withMessages([
        'username' => trans('auth.failed'),
      ]);
    }

    RateLimiter::clear($this->throttleKey($request));
    $request->session()->regenerate();

    return redirect()->intended('/');
  }

  public function destroy(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('success', 'You have been successfully logged out');
  }

  protected function throttleKey(Request $request): string
  {
    return Str::lower($request->input('username')) . '|' . $request->ip();
  }

  protected function ensureIsNotRateLimited(Request $request): void
  {
    if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
      return;
    }

    $seconds = RateLimiter::availableIn($this->throttleKey($request));
    throw ValidationException::withMessages([
      'username' => __('Too many login attempts. Please try again in :seconds seconds', ['seconds' => $seconds]),
    ])->status(429);
  }
}
