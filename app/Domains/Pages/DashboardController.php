<?php

namespace App\Domains\Pages;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
  public function view()
  {
    return view('content.pages.dashboard');
  }
}
