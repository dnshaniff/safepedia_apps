<?php

namespace App\Http\Controllers\hr;

use Throwable;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeAgreementRequest;
use App\Models\EmployeeAgreement as ModelsEmployeeAgreement;

class EmployeeAgreement extends Controller
{
  public function index(Request $request, Employee $employee)
  {
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $query = ModelsEmployeeAgreement::query()->where('employee_agreements.employee_id', $employee->id)
      ->with(['creator:id,username', 'creator.employee:id,user_id,full_name'])
      ->when($isAdmin, fn($q) => $q->withTrashed());

    $totalData = $query->count();

    $agreements = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($agreements)) {
      $ids = $request->input('start');
      foreach ($agreements as $agreement) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $agreement->id;
        $nestedData['agreement_type'] = $agreement->agreement_type;
        $nestedData['date'] = $this->formatAgreementDatePeriod($agreement);
        $nestedData['notes'] = $agreement->notes ?? '';
        $nestedData['creator'] = $agreement->creator?->display_name ?? '-';
        $nestedData['deleted_at'] = $agreement->deleted_at;

        $data[] = $nestedData;
      }
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalData),
      'code' => 200,
      'data' => $data,
    ]);
  }

  private function formatAgreementDatePeriod(ModelsEmployeeAgreement $a): string
  {
    if (!in_array($a->agreement_type, ['Contract', 'Extension'], true)) {
      return $a->effective_date ? Carbon::parse($a->effective_date)->format('d F Y') : '-';
    }

    if (!$a->start_date || !$a->end_date) {
      return '-';
    }

    $start = Carbon::parse($a->start_date);
    $end   = Carbon::parse($a->end_date);

    if ($start->isSameMonth($end) && $start->isSameYear($end)) {
      return sprintf(
        '%d – %d, %s %d',
        $start->day,
        $end->day,
        $start->format('F'),
        $start->year
      );
    }

    if ($start->isSameYear($end)) {
      return sprintf(
        '%d %s – %d %s, %d',
        $start->day,
        $start->format('F'),
        $end->day,
        $end->format('F'),
        $start->year
      );
    }

    return sprintf(
      '%d %s %d – %d %s %d',
      $start->day,
      $start->format('F'),
      $start->year,
      $end->day,
      $end->format('F'),
      $end->year
    );
  }

  public function store(Employee $employee, StoreEmployeeAgreementRequest $request)
  {
    try {
      $data = $request->validated();
      $data['employee_id'] = $employee->id;

      $employeeAgreement = DB::transaction(fn() => ModelsEmployeeAgreement::create($data));

      return response()->json(['status' => 'success', 'message' => "Agreement: {$employeeAgreement->agreement_type} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function edit(Employee $employee, ModelsEmployeeAgreement $employeeAgreement)
  {
    return response()->json($employeeAgreement, 200);
  }

  public function update(Employee $employee, Request $request, ModelsEmployeeAgreement $employeeAgreement)
  {
    try {
      DB::transaction(fn() => $employeeAgreement->update($request->validated()));

      return response()->json(['status' => 'success', 'message' => "Agreement: {$employeeAgreement->agreement_type} updated successfully", 200]);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function destroy(Employee $employee, ModelsEmployeeAgreement $employeeAgreement)
  {
    try {
      $employeeAgreement->delete();

      return response()->json(['status' => 'success', 'message' => "Agreement: {$employeeAgreement->agreement_type} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function restore(Employee $employee, string $id)
  {
    $employeeAgreement = ModelsEmployeeAgreement::withTrashed()->findOrFail($id);

    try {
      if ($employeeAgreement->trashed()) {
        $employeeAgreement->restore();

        return response()->json(['status' => 'success', 'message' => "Agreement: {$employeeAgreement->agreement_type} successfully restored"], 200);
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

  public function force(Employee $employee, string $id)
  {
    $employeeAgreement = ModelsEmployeeAgreement::withTrashed()->findOrFail($id);

    try {
      if ($employeeAgreement->trashed()) {
        $employeeAgreement->forceDelete();

        return response()->json(['status' => 'success', 'message' => 'Employee agreement delete successfully'], 200);
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
