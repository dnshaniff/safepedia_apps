<?php

namespace App\Domains\Pages;

use App\Domains\Pages\Services\DashboardService;
use App\Http\Controllers\Controller;
use App\Models\WarehouseConstruction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function view()
  {
    $cards = [
      'total_construction' => WarehouseConstruction::count(),
      'progress' => WarehouseConstruction::whereIn('status', ['draft', 'pending', 'returned'])->count(),
      'approved' => WarehouseConstruction::where('status', 'approved')->count(),
    ];

    return view('content.pages.dashboard', compact('cards'));
  }

  public function index(Request $request, DashboardService $service)
  {
    return response()->json(
      $service->execute([
        'search' => $request->input('search.value'),
        'start' => $request->input('start'),
        'length' => $request->input('length'),
        'draw' => $request->input('draw'),
      ])
    );
  }

  public function chart(Request $request, DashboardService $service)
  {
    return response()->json($service->chart($request->input('period', now()->format('Y-m'))), 200);
  }
}
