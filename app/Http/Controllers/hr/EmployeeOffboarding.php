<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeOffboarding extends Controller
{
  public function view()
  {
    return view('content.hr.employee_offboardings');
  }

  public function index(Request $request)
  {
    $search = $request->input('search.value');

    $from = now()->subDays(2)->toDateString();
    $until = now()->addMonths(2)->toDateString();

    $query = Employee::query()
      ->select([
        'employees.id',
        'employees.employee_code',
        'employees.full_name',
        'employees.company_id',
        'employees.org_unit_id',
        'employees.job_title_id',
        'employees.hrbp_id',
        'employees.employment_type',
      ])
      ->where('employees.employment_type', '!=', 'Resign')
      ->whereExists(function ($exists) use ($from, $until) {
        $exists->selectRaw(1)
          ->from('employee_agreements as ea')
          ->whereColumn('ea.employee_id', 'employees.id')
          ->whereNull('ea.deleted_at')
          ->whereRaw('ea.id = (
          SELECT ea2.id
          FROM employee_agreements ea2
          WHERE ea2.employee_id = employees.id
            AND ea2.deleted_at IS NULL
          ORDER BY
            COALESCE(ea2.effective_date, ea2.end_date, ea2.start_date, ea2.created_at) DESC,
            ea2.id DESC
          LIMIT 1
      )')
          ->where(function ($q) use ($from, $until) {
            $q->where(function ($qq) use ($from, $until) {
              $qq->where('ea.agreement_type', 'Contract')
                ->whereNotNull('ea.end_date')
                ->whereBetween('ea.end_date', [$from, $until]);
            })->orWhere(function ($qq) use ($from, $until) {
              $qq->where('ea.agreement_type', 'Resignation')
                ->whereNotNull('ea.effective_date')
                ->whereBetween('ea.effective_date', [$from, $until]);
            });
          });
      })
      ->with([
        'lastDayAgreement:id,employee_id,agreement_type,end_date,effective_date',
      ]);

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
        $ag = $employee->lastDayAgreement;

        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $employee->id;
        $nestedData['employee_code'] = $employee->employee_code;
        $nestedData['full_name'] = $employee->full_name;
        $nestedData['company'] = $employee->company ? $employee->company->company_code : '';
        $nestedData['org_unit'] = $employee->orgUnit ? $employee->orgUnit->unit_name : '';
        $nestedData['job_title'] = $employee->jobTitle ? $employee->jobTitle->title_name : '';
        $nestedData['employment_type'] = $employee->employment_type;
        $nestedData['hrbp'] = $employee->hrbp ? $employee->hrbp->full_name : '';
        $nestedData['last_day'] = $ag ? ($ag->agreement_type === 'Contract' ? $ag->end_date : $ag->effective_date) : null;

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
}
