<?php

namespace App\Domains\Approvals;

use App\Domains\Approvals\Queries\IndexService;
use App\Domains\Approvals\Requests\StoreRequest;
use App\Domains\Approvals\Requests\UpdateRequest;
use App\Domains\Approvals\Services\StoreService;
use App\Domains\Approvals\Services\TerminateService;
use App\Domains\Approvals\Services\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApprovalController extends Controller
{
  public function view()
  {
    return view('content.pages.approvals');
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
      $approval = $service->execute($request->validated());

      return response()->json(['status' => 'success', 'message' => "{$approval->approval_role} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating approval', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function edit(Approval $approval)
  {
    $approval->load('employee');
    return response()->json($approval, 200);
  }

  public function update(UpdateRequest $request, Approval $approval, UpdateService $service)
  {
    try {
      $service->execute($approval, $request->validated());

      return response()->json(['status' => 'success', 'message' => "{$approval->approval_role} updated successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating approval', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(Approval $approval, TerminateService $service)
  {
    try {
      $service->delete($approval);

      return response()->json(['status' => 'success', 'message' => "{$approval->approval_role} deleted successfully"], 200);
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
    $approval = Approval::withTrashed()->findOrFail($id);

    try {
      if ($approval->trashed()) {
        $service->restore($approval);

        return response()->json(['status' => 'success', 'message' => "{$approval->approval_role} successfully restored"], 200);
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
    $approval = Approval::withTrashed()->findOrFail($id);

    try {
      if ($approval->trashed()) {
        $service->force($approval);

        return response()->json(['status' => 'success', 'message' => 'Approval permanent delete successfully'], 200);
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
}
