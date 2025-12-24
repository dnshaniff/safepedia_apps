<?php

namespace App\Http\Controllers\hr;

use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    $fmt = fn($d) => $d ? Carbon::parse($d)->format('d/m/Y') : null;

    $effective = $fmt($a->effective_date);
    $start = $fmt($a->start_date);
    $end = $fmt($a->end_date);

    if (in_array($a->agreement_type, ['Contract', 'Extension'], true)) {
      $period = trim(($start ?? '-') . ' - ' . ($end ?? '-'));
      return $effective ? "{$effective} | {$period}" : $period;
    }

    return $effective ?? '-';
  }

  public function store(Request $request)
  {
    //
  }

  public function edit(ModelsEmployeeAgreement $employeeAgreement)
  {
    //
  }

  public function update(Request $request, ModelsEmployeeAgreement $employeeAgreement)
  {
    //
  }

  public function destroy(ModelsEmployeeAgreement $employeeAgreement)
  {
    //
  }

  public function restore(string $id)
  {
    $employeeAgreement = ModelsEmployeeAgreement::withTrashed()->findOrFail($id);
  }

  public function force(string $id)
  {
    $employeeAgreement = ModelsEmployeeAgreement::withTrashed()->findOrFail($id);
  }
}
