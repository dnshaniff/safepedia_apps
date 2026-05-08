<?php

namespace App\Domains\Pages\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutService
{
  public function execute(Request $request): void
  {
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();
  }
}
