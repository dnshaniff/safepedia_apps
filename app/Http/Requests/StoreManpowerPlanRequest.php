<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreManpowerPlanRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'org_unit_id' => 'required|exists:org_units,id',
      'position_title' => 'required|string',
      'planned_date' => 'required|date|after_or_equal:today',
      'number_positions' => 'required|integer|min:1',
      'devices' => 'required|array|min:1',
      'devices.*' => 'exists:asset_types,id',
      'notes' => 'nullable|string'
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
