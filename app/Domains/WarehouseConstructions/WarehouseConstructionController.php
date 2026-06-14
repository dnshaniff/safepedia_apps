<?php

namespace App\Domains\WarehouseConstructions;

use App\Domains\WarehouseConstructions\Queries\IndexService;
use App\Domains\WarehouseConstructions\Requests\ApprovalRequest;
use App\Domains\WarehouseConstructions\Requests\StoreRequest;
use App\Domains\WarehouseConstructions\Requests\UpdateRequest;
use App\Domains\WarehouseConstructions\Services\ApprovalService;
use App\Domains\WarehouseConstructions\Services\CancelService;
use App\Domains\WarehouseConstructions\Services\StoreService;
use App\Domains\WarehouseConstructions\Services\SubmitService;
use App\Domains\WarehouseConstructions\Services\TerminateService;
use App\Domains\WarehouseConstructions\Services\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\WarehouseConstruction;
use App\Models\WarehouseConstructionApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class WarehouseConstructionController extends Controller
{
  public function view()
  {
    return view('content.warehouse_constructions.index');
  }

  public function index(Request $request, IndexService $service)
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

  public function store(StoreRequest $request, StoreService $service)
  {
    try {
      $warehouseConstruction = $service->execute($request->validated());

      return response()->json(['status' => 'success', 'message' => "{$warehouseConstruction->warehouse_name} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating construction', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function show(WarehouseConstruction $warehouseConstruction)
  {
    $warehouseConstruction->load([
      'items',
      'documents',
      'creator',
      'approvals.employee',
      'approvals.approval',
    ]);

    return view('content.warehouse_constructions.show', compact('warehouseConstruction'));
  }

  public function edit(WarehouseConstruction $warehouseConstruction)
  {
    $warehouseConstruction->load([
      'items:id,warehouse_construction_id,item_name,quantity,unit_price,line_total',
      'documents:id,warehouse_construction_id,original_name,file_name,file_size',
    ]);

    return response()->json($warehouseConstruction);
  }

  public function update(UpdateRequest $request, WarehouseConstruction $warehouseConstruction, UpdateService $service)
  {
    try {
      $service->execute($warehouseConstruction, $request->validated());

      return response()->json(['status' => 'success', 'message' => "{$warehouseConstruction->warehouse_name} updated successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating construction', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(WarehouseConstruction $warehouseConstruction, TerminateService $service)
  {
    try {
      $service->delete($warehouseConstruction);

      return response()->json(['status' => 'success', 'message' => "{$warehouseConstruction->warehouse_name} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function restore(string $id, TerminateService $service)
  {
    $warehouseConstruction = WarehouseConstruction::withTrashed()->findOrFail($id);

    try {
      if ($warehouseConstruction->trashed()) {
        $service->restore($warehouseConstruction);

        return response()->json(['status' => 'success', 'message' => "{$warehouseConstruction->warehouse_name} successfully restored"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function force(string $id, TerminateService $service)
  {
    $warehouseConstruction = WarehouseConstruction::withTrashed()->findOrFail($id);

    try {
      if ($warehouseConstruction->trashed()) {
        $service->force($warehouseConstruction);

        return response()->json(['status' => 'success', 'message' => 'Construction permanent delete successfully'], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function submit(WarehouseConstruction $warehouseConstruction, SubmitService $service)
  {
    try {
      $service->execute($warehouseConstruction);

      return response()->json([
        'status' => 'success',
        'message' => "{$warehouseConstruction->warehouse_name} submitted successfully",
      ], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while submitting construction', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function cancel(WarehouseConstruction $warehouseConstruction, CancelService $service)
  {
    try {
      $service->execute($warehouseConstruction);

      return response()->json([
        'status' => 'success',
        'message' => "{$warehouseConstruction->warehouse_name} canceled successfully",
      ], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while canceling construction', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function approval(ApprovalRequest $request, WarehouseConstruction $warehouseConstruction, WarehouseConstructionApproval $warehouseConstructionApproval, ApprovalService $service)
  {
    try {
      $service->execute($warehouseConstruction, $warehouseConstructionApproval, $request->validated());

      return response()->json(['status' => 'success', 'message' => 'Request has been ' . $request->action], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while canceling construction', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }
}
