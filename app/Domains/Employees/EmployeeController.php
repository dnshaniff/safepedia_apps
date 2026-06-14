<?php

namespace App\Domains\Employees;

use App\Domains\Employees\Queries\IndexService;
use App\Domains\Employees\Queries\SelectService;
use App\Domains\Employees\Requests\StoreRequest;
use App\Domains\Employees\Requests\UpdateRequest;
use App\Domains\Employees\Requests\UserRequest;
use App\Domains\Employees\Services\StoreService;
use App\Domains\Employees\Services\TerminateService;
use App\Domains\Employees\Services\UpdateService;
use App\Domains\Employees\Services\UserService;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmployeeController extends Controller
{
  public function view()
  {
    return view('content.pages.employees');
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
        'draw' => $request->input('draw'),
      ])
    );
  }

  public function store(StoreRequest $request, StoreService $service)
  {
    try {
      $employee = $service->execute($request->validated());

      return response()->json(['status' => 'success', 'message' => "{$employee->full_name} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating employee', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function edit(Employee $employee)
  {
    return response()->json($employee, 200);
  }

  public function update(UpdateRequest $request, Employee $employee, UpdateService $service)
  {
    try {
      $service->execute($employee, $request->validated());

      return response()->json(['status' => 'success', 'message' => "{$employee->full_name} updated successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while updating employee', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function user(UserRequest $request, Employee $employee, UserService $service)
  {
    try {
      $service->execute($employee, $request->validated());

      return response()->json(['status' => 'success', 'message' => "{$employee->full_name} updated successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating user for employee', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
    }
  }

  public function destroy(Employee $employee, TerminateService $service)
  {
    try {
      $service->delete($employee);

      return response()->json(['status' => 'success', 'message' => "{$employee->full_name} deleted successfully"], 200);
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
    $employee = Employee::withTrashed()->findOrFail($id);

    try {
      if ($employee->trashed()) {
        $service->restore($employee);

        return response()->json(['status' => 'success', 'message' => "{$employee->full_name} successfully restored"], 200);
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
    $employee = Employee::withTrashed()->findOrFail($id);

    try {
      if ($employee->trashed()) {
        $service->force($employee);

        return response()->json(['status' => 'success', 'message' => 'Employee permanent delete successfully'], 200);
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
