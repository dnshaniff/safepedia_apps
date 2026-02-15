<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $employeeId = $this->route('employee')?->id;

    return [
      'employee_code' => ['required', 'string', Rule::unique('employees', 'employee_code')->ignore($employeeId)],
      'full_name' => ['required', 'string'],
      'hrbp_id' => ['nullable', 'exists:employees,id'],
      'manager_id' => ['nullable', 'exists:employees,id'],
      'join_date' => ['required', 'date'],
      'company_id' => ['required', 'exists:companies,id'],
      'org_unit_id' => ['required', 'exists:org_units,id'],
      'job_title_id' => ['required', 'exists:job_titles,id'],
      'employment_status' => ['required', Rule::in(['Colleague', 'Contract', 'Freelance', 'Intern', 'Probation', 'Resign'])],
      'office_email' => ['nullable', 'email', Rule::unique('employees', 'office_email')->ignore($employeeId)],
      'personal_email' => ['nullable', 'email', Rule::unique('employees', 'personal_email')->ignore($employeeId)],
      'phone_number' => ['nullable', Rule::unique('employees', 'phone_number')->ignore($employeeId)],
      'gender' => ['required', Rule::in(['Female', 'Male'])],
      'date_of_birth' => ['required', 'date'],
      'ktp_number' => ['required', Rule::unique('employees', 'ktp_number')->ignore($employeeId)]
    ];
  }

  protected function prepareForValidation(): void
  {
    $this->merge([
      'hrbp_id'    => $this->input('hrbp_id')    === '' ? null : $this->input('hrbp_id'),
      'manager_id' => $this->input('manager_id') === '' ? null : $this->input('manager_id'),
    ]);
  }

  public function withValidator($validator): void
  {
    $validator->after(function ($validator) {
      $employee  = $this->route('employee');
      $managerId = $this->input('manager_id');

      if ($employee && $managerId && (string) $managerId === (string) $employee->id) {
        $validator->errors()->add(
          'manager_id',
          'Invalid selection: an employee cannot be assigned as their own manager'
        );
      }
    });
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
