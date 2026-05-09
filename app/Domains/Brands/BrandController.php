<?php

namespace App\Domains\Brands;

use App\Domains\Brands\Queries\IndexService;
use App\Domains\Brands\Queries\SelectService;
use App\Domains\Brands\Requests\StoreRequest;
use App\Domains\Brands\Requests\UpdateRequest;
use App\Domains\Brands\Services\StoreService;
use App\Domains\Brands\Services\TerminateService;
use App\Domains\Brands\Services\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class BrandController extends Controller
{
  public function view()
  {
    return view('content.brands.index');
  }

  public function select(Request $request, SelectService $service)
  {
    $search = trim((string) $request->get('q', ''));
    $page = max(1, (int) $request->get('page', 1));

    $perPage = max(1, min(100, (int) $request->get('per', 10)));

    $result = $service->execute($search, $page, $perPage);

    return response()->json($result);
  }

  public function index(Request $request, IndexService $service)
  {
    return response()->json(
      $service->execute([
        'search' => $request->input('search.value'),
        'start' => $request->input('start'),
        'length' => $request->input('length'),
        'draw' => $request->input('draw')
      ])
    );
  }

  public function store(StoreRequest $request, StoreService $service)
  {
    try {
      $brand = $service->execute($request->validated());

      return response()->json(['status' => 'success', 'message' => "Brand {$brand->name} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating brand', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function edit(Brand $brand)
  {
    return response()->json($brand, 200);
  }

  public function update(UpdateRequest $request, Brand $brand, UpdateService $service)
  {
    try {
      $service->execute($brand, $request->validated());

      return response()->json(['status' => 'success', 'message' => "Brand {$brand->name} updated successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating brand', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(Brand $brand, TerminateService $service)
  {
    try {
      $service->delete($brand);

      return response()->json(['status' => 'success', 'message' => "Brand {$brand->name} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while deleting brand', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function restore(string $id, TerminateService $service)
  {
    $brand = Brand::withTrashed()->findOrFail($id);

    try {
      if ($brand->trashed()) {
        $service->restore($brand);

        return response()->json(['status' => 'success', 'message' => "Brand {$brand->name} restored successfully"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 422);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while restoring brand', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function force(string $id, TerminateService $service)
  {
    $brand = Brand::withTrashed()->findOrFail($id);

    try {
      if ($brand->trashed()) {
        $service->force($brand);

        return response()->json(['status' => 'success', 'message' => "Brand permanent delete successfully"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 422);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while forcing brand deletion', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }
}
