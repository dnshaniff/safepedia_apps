<?php

namespace App\Domains\Approvals\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $approvalId = $this->route('approval')?->id;

    return [
      'approval_role' => 'required|min:4|max:50',
      'sequence' => ['required', 'integer', 'min:1', Rule::unique('approvals', 'sequence')->ignore($approvalId)],
      'employee_id' => 'required|exists:employees,id',
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
