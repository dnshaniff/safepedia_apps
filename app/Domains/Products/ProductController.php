<?php

namespace App\Domains\Products;

use App\Domains\Products\Queries\IndexService;
use App\Domains\Products\Requests\StoreRequest;
use App\Domains\Products\Requests\UpdateRequest;
use App\Domains\Products\Services\StoreService;
use App\Domains\Products\Services\TerminateService;
use App\Domains\Products\Services\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductController extends Controller
{
  public function view()
  {
    return view('content.products.index');
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
      $service->execute($request->validated());

      return response()->json(['status' => 'success', 'message' => 'Product created succefully'], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating product', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function edit(Product $product)
  {
    $product->load('brand:id,name', 'images');

    return response()->json($product, 200);
  }

  public function update(UpdateRequest $request, Product $product, UpdateService $service)
  {
    try {
      $service->execute($product, $request->validated());

      return response()->json(['status' => 'success', 'message' => 'Product updated succefully'], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating product', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(Product $product, TerminateService $service)
  {
    try {
      $service->delete($product);

      return response()->json(['status' => 'success', 'message' => "Product deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while deleting product', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function restore(string $id, TerminateService $service)
  {
    $product = Product::withTrashed()->findOrFail($id);

    try {
      if ($product->trashed()) {
        $service->restore($product);

        return response()->json(['status' => 'success', 'message' => "Product restored successfully"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 422);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while restoring product', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function force(string $id, TerminateService $service)
  {
    $product = Product::withTrashed()->findOrFail($id);

    try {
      if ($product->trashed()) {
        $service->force($product);

        return response()->json(['status' => 'success', 'message' => "Product permanent delete successfully"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 422);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while forcing product deletion', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }
}
