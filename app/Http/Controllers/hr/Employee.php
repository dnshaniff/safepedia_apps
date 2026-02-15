<?php

namespace App\Http\Controllers\hr;

use Throwable;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\ImportEmployeesJob;
use App\Mail\EmployeeUserCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreUserRequest;
use App\Models\Employee as ModelsEmployee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\ImportEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

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

    $query = ModelsEmployee::query()->select([
      'employees.id',
      'employees.user_id',
      'employees.employee_code',
      'employees.full_name',
      'employees.company_id',
      'employees.org_unit_id',
      'employees.job_title_id',
      'employees.hrbp_id',
      'employees.join_date',
      'employees.employment_status',
      'employees.deleted_at',
    ])->with([
      'company:id,company_code',
      'orgUnit:id,unit_name',
      'jobTitle:id,title_name',
      'hrbp:id,full_name',
    ])->when($isAdmin, fn($q) => $q->withTrashed());

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('full_name', 'LIKE', "%{$search}%")
          ->orWhere('employee_code', 'LIKE', "%{$search}%")
          ->orWhereHas('orgUnit', function ($q2) use ($search) {
            $q2->where('unit_name', 'LIKE', "%{$search}%");
          })
          ->orWhereHas('jobTitle', function ($q3) use ($search) {
            $q3->where('title_name', 'LIKE', "%{$search}%");
          });
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
        $nestedData['company'] = $employee->company ? $employee->company->company_code : '';
        $nestedData['org_unit'] = $employee->orgUnit ? $employee->orgUnit->unit_name : '';
        $nestedData['job_title'] = $employee->jobTitle ? $employee->jobTitle->title_name : '';
        $nestedData['join_date'] = $employee->join_date->format('d/m/Y');
        $nestedData['employment_status'] = $employee->employment_status;
        $nestedData['hrbp'] = $employee->hrbp ? $employee->hrbp->full_name : '';
        $nestedData['user_id'] = $employee->user_id;
        $nestedData['can_store_user'] = auth()->user()->can('employees.storeUser');
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

  public function store(StoreEmployeeRequest $request)
  {
    try {
      $employee = DB::transaction(fn() => ModelsEmployee::create($request->validated()));

      return response()->json(['status' => 'success', 'message' => "Employee: {$employee->full_name} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function storeUser(StoreUserRequest $request, ModelsEmployee $employee)
  {
    if ($employee->user_id) {
      return response()->json(['status' => 'info', 'message' => 'Employee already has a user'], 400);
    }

    try {
      $validated = $request->validated();
      $password = Str::random(12);
      $email = $employee->office_email ?: $employee->personal_email;

      $user = DB::transaction(function () use ($validated, $employee, $password) {
        $user = User::create([
          'username' => $validated['username'],
          'password' => Hash::make($password),
        ]);

        $user->syncRoles([$validated['role']]);
        $employee->update(['user_id' => $user->id]);
      });

      Mail::to($email)->queue(new EmployeeUserCreated($user, $password));

      return response()->json(['status' => 'success', 'message' => "User: {$user->username} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while creating user', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request'], 500);
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

  public function update(UpdateEmployeeRequest $request, ModelsEmployee $employee)
  {
    try {
      DB::transaction(fn() => $employee->update($request->validated()));

      return response()->json(['status' => 'success', 'message' => "Employee: {$employee->full_name} updated successfully"], 200);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
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
        'trace' => $e->getTraceAsString(),
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
        'trace' => $e->getTraceAsString(),
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
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function import(ImportEmployeeRequest $request)
  {
    try {
      $file = $request->file('file');
      $path = $file->store('imports/employees');

      ImportEmployeesJob::dispatch($path, auth()->id());

      return response()->json(['status' => 'success', 'message' => 'Import process started'], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return response()->json(['status' => 'danger', 'message' => 'Failed to start import process'], 500);
    }
  }
}
