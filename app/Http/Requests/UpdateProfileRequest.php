<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $user = User::where('username', $this->route('username'))->first();
    $employeeId = $user?->employee?->id;

    return [
      'username' => [
        'required',
        'min:4',
        'max:50',
        Rule::unique('users', 'username')->ignore($user?->id),
      ],
      'personal_email' => ['nullable', 'email', Rule::unique('employees', 'personal_email')->ignore($employeeId)],
      'password' => ['nullable', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/', 'confirmed'],
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
