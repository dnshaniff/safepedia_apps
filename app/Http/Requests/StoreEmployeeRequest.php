<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEmployeeRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'employee_code' => 'required|string|unique:employees,employee_code',
      'full_name' => 'required|string',
      'hrbp_id' => 'nullable|exists:employees,id',
      'manager_id' => 'nullable|exists:employees,id',
      'join_date' => 'required|date',
      'company_id' => 'required|exists:companies,id',
      'org_unit_id' => 'required|exists:org_units,id',
      'job_title_id' => 'required|exists:job_titles,id',
      'employment_status' => 'required|in:Colleague,Contract,Freelance,Intern,Probation,Resign',
      'office_email' => 'nullable|email|unique:employees,office_email',
      'personal_email' => 'nullable|email|unique:employees,personal_email',
      'phone_number' => 'nullable|unique:employees,phone_number',
      'gender' => 'required|in:Female,Male',
      'date_of_birth' => 'required|date',
      'ktp_number' => 'required|unique:employees,ktp_number'
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
