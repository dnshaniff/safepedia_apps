<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
  public function view(Request $request)
  {
    return view('content.pages.dashboard');
  }
}
