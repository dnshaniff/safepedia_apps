<?php

namespace App\Domains\Users\Requests;

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
    $userId = $this->route('user')?->id;

    return [
      'name' => 'required|min:4|max:50',
      'email' => ['required', 'email:rfc,dns', Rule::unique('users', 'email')->ignore($userId)],
      'username' => ['required', 'min:4', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
      'role' => ['required'],
      'status' => ['required'],
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
