<?php

namespace App\Http\Controllers\hr;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Employee as ModelsEmployee;
use Illuminate\Validation\ValidationException;

class Employee extends Controller
{
  public function view()
  {
    return view('content.hr.employees');
  }

  public function select(Request $request)
  {
    $q     = trim((string) $request->get('q', ''));
    $page  = max(1, (int) $request->get('page', 1));
    $per   = max(1, min(100, (int) $request->get('per', 10)));

    $orgUnitCode = $request->get('org_unit_code');
    $orgUnitType = $request->get('org_unit_type');

    $query = ModelsEmployee::query()->select(['employees.id', 'employees.full_name'])->with('orgUnit:id,unit_code,unit_type');

    if (!empty($orgUnitCode) || !empty($orgUnitType)) {
      $query->whereHas('orgUnit', function ($q) use ($orgUnitCode, $orgUnitType) {
        if (!empty($orgUnitCode)) {
          $q->where('unit_code', $orgUnitCode);
        }

        if (!empty($orgUnitType)) {
          $q->where('unit_type', $orgUnitType);
        }
      });
    }

    if ($q !== '') {
      $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      foreach ($tokens as $t) {
        $t = str_replace(['%', '_'], ['\\%', '\\_'], $t);
        $query->where('full_name', 'LIKE', "%{$t}%");
      }
    }

    $query->orderBy('full_name');

    $rows = $query->skip(($page - 1) * $per)->take($per + 1)->get();

    $more = $rows->count() > $per;
    if ($more) $rows = $rows->slice(0, $per);

    return response()->json([
      'results' => $rows->map(fn($r) => [
        'id'   => $r->id,
        'text' => $r->full_name,
      ])->values(),
      'more' => $more
    ]);
  }

  public function index(Request $request)
  {
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $search = $request->input('search.value');

    $query = ModelsEmployee::query()
      ->select([
        'employees.id',
        'employees.employee_code',
        'employees.full_name',
        'employees.company_id',
        'employees.org_unit_id',
        'employees.job_title_id',
        'employees.hrbp_id',
        'employees.join_date',
        'employees.employment_type',
        'employees.deleted_at',
      ])
      ->with([
        'company:id,company_code',
        'orgUnit:id,unit_name',
        'jobTitle:id,title_name',
        'hrbp:id,full_name',
      ])
      ->when($isAdmin, fn($q) => $q->withTrashed());


    $totalData = $query->count();

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('full_name', 'LIKE', "%{$search}%");
      });
    }

    $totalFiltered = $query->count();

    $employees = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($employees)) {
      $ids = $request->input('start');
      foreach ($employees as $employee) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $employee->id;
        $nestedData['employee_code'] = $employee->employee_code;
        $nestedData['full_name'] = $employee->full_name;
        $nestedData['company'] = $employee->company ? $employee->company->company_code : '-';
        $nestedData['org_unit'] = $employee->orgUnit ? $employee->orgUnit->unit_name : '-';
        $nestedData['job_title'] = $employee->jobTitle ? $employee->jobTitle->title_name : '-';
        $nestedData['join_date'] = $employee->join_date->format('d/m/Y');
        $nestedData['employment_type'] = $employee->employment_type;
        $nestedData['hrbp'] = $employee->hrbp ? $employee->hrbp->full_name : '-';
        $nestedData['deleted_at'] = $employee->deleted_at;

        $data[] = $nestedData;
      }
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'code' => 200,
      'data' => $data,
    ]);
  }

  public function store(Request $request)
  {
    try {
      $validated = $request->validate([
        'employee_code' => 'required|string|unique:employees,employee_code',
        'full_name' => 'required|string',
        'hrbp_id' => 'nullable|exists:employees,id',
        'manager_id' => 'nullable|exists:employees,id',
        'join_date' => 'required|date',
        'company_id' => 'required|exists:companies,id',
        'org_unit_id' => 'required|exists:org_units,id',
        'job_title_id' => 'required|exists:job_titles,id',
        'employment_type' => 'required|in:Colleague,Contract,Freelance,Intern,Probation,Resign',
        'office_email' => 'nullable|email|unique:employees,office_email',
        'personal_email' => 'nullable|email|unique:employees,personal_email',
        'phone_number' => 'nullable|unique:employees,phone_number',
        'gender' => 'required|in:Female,Male',
        'date_of_birth' => 'required|date'
      ]);
      $validated['created_by'] = auth()->user()->id;

      $employee = DB::transaction(function () use ($validated) {
        return ModelsEmployee::create($validated);
      });

      return response()->json(['status' => 'success', 'message' => "Employee: {$employee->full_name} created successfully"], 201);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message, 'errors' => $e->errors()], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function show(ModelsEmployee $employee)
  {
    $employee->load([
      'company:id,company_name',
      'orgUnit:id,unit_name',
      'jobTitle:id,title_name',
      'manager:id,full_name',
      'hrbp:id,full_name',
      'creator.employee:id,user_id,full_name'
    ]);

    return view('content.hr.employees-show', compact('employee'));
  }

  public function edit(ModelsEmployee $employee)
  {
    $employee->load(['hrbp:id,full_name', 'manager:id,full_name', 'company:id,company_name', 'orgUnit:id,unit_name', 'jobTitle:id,title_name']);

    return response()->json($employee, 200);
  }

  public function update(Request $request, ModelsEmployee $employee)
  {
    try {
      $validated = $request->validate([
        'employee_code' => 'required|string|unique:employees,employee_code,' . $employee->id,
        'full_name' => 'required|string',
        'hrbp_id' => 'nullable|exists:employees,id',
        'manager_id' => 'nullable|exists:employees,id',
        'join_date' => 'required|date',
        'company_id' => 'required|exists:companies,id',
        'org_unit_id' => 'required|exists:org_units,id',
        'job_title_id' => 'required|exists:job_titles,id',
        'employment_type' => 'required|in:Colleague,Contract,Freelance,Intern,Probation,Resign',
        'office_email' => 'nullable|email|unique:employees,office_email,' . $employee->id,
        'personal_email' => 'nullable|email|unique:employees,personal_email,' . $employee->id,
        'phone_number' => 'nullable|unique:employees,phone_number,' . $employee->id,
        'gender' => 'required|in:Female,Male',
        'date_of_birth' => 'required|date'
      ]);

      if (!empty($validated['manager_id']) && $validated['manager_id'] === $employee->id) {
        return response()->json(['status' => 'info', 'message' => 'Invalid selection: an employee cannot be assigned as their own manager'], 422);
      }

      DB::transaction(function () use ($validated, $employee) {
        $employee->update($validated);
      });

      return response()->json(['status' => 'success', 'message' => "Employee: {$employee->full_name} updated successfully"], 200);
    } catch (ValidationException $e) {
      $message = collect($e->errors())->flatten()->implode("\n");
      return response()->json(['status' => 'danger', 'message' => $message, 'errors' => $e->errors()], 422);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function destroy(ModelsEmployee $employee)
  {
    try {
      $employee->delete();

      return response()->json(['status' => 'success', 'message' => "Employee: {$employee->full_name} deleted successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function restore(string $id)
  {
    $employee = ModelsEmployee::withTrashed()->findOrFail($id);

    try {
      if ($employee->trashed()) {
        $employee->restore();

        return response()->json(['status' => 'success', 'message' => "Employee: {$employee->full_name} successfully restored"], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function force(string $id)
  {
    $employee = ModelsEmployee::withTrashed()->findOrFail($id);

    if ($employee->user()->exists()) {
      return response()->json(['status' => 'info', 'message' => 'Data cannot be deleted because it is associated with other records'], 422);
    }

    try {
      if ($employee->trashed()) {
        $employee->forceDelete();

        return response()->json(['status' => 'success', 'message' => 'Employee permanent delete successfully'], 200);
      } else {
        return response()->json(['status' => 'info', 'message' => 'Data is not in trash'], 200);
      }
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }
}
