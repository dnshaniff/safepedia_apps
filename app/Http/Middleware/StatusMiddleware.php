<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StatusMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next)
  {
    if (Auth::check()) {
      $user = Auth::user();
      if ($user->status === 'active') {
        return $next($request);
      } else {
        Auth::logout();
        return redirect()
          ->route('auth-login.index')
          ->with('error', 'Your account is inactive, please contact the administrator');
      }
    }
  }
}
