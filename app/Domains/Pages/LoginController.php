<?php

namespace App\Domains\Pages;

use App\Domains\Pages\Requests\LoginRequest;
use App\Domains\Pages\Services\LoginService;
use App\Domains\Pages\Services\LogoutService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
      $service->execute($request);

      return redirect()->intended('/dashboard');
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
}
