<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (! Auth::check()) {
      return $next($request);
    }

    $user = Auth::user();

    if (! $user->two_factor_enabled) {
      return $next($request);
    }

    if (! session('two_factor_verified')) {
      if (! $request->routeIs('twofactor.index', 'twofactor.store')) {
        return redirect()->route('twofactor.index');
      }
    }

    return $next($request);
  }
}
