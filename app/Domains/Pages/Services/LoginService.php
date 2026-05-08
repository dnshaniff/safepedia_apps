<?php

namespace App\Domains\Pages\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginService
{
  public function execute(Request $request): void
  {
    $credentials = $request->validated();

    $this->ensureIsNotRateLimited($request);

    $remember = (bool) $request->boolean('remember');

    if (! Auth::attempt($credentials, $remember)) {
      RateLimiter::hit($this->throttleKey($request), 300);

      throw ValidationException::withMessages(['username' => trans('auth.failed')]);
    }

    RateLimiter::clear($this->throttleKey($request));

    $request->session()->regenerate();
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

    throw ValidationException::withMessages(['username' => __('Too many login attempts. Please try again in :seconds seconds', ['seconds' => $seconds])])->status(429);
  }
}
