<?php

namespace App\Http\Controllers\pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

    if (!Auth::validate($credentials)) {
      return redirect()->back()->withErrors(trans('auth.failed'));
    }

    $user = Auth::getProvider()->retrieveByCredentials($credentials);
    Auth::login($user);

    return redirect()->intended('/');
  }

  public function destroy()
  {
    Session::flush();
    Auth::logout();

    return redirect()->route('auth-login.index')->with('success', 'You have been successfully logged out');
  }
}
