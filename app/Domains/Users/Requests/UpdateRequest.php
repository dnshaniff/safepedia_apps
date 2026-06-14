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
      'username' => ['required', 'min:4', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
      'password' => 'nullable|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/|confirmed',
      'role' => 'required',
      'otp' => 'nullable|digits:6',
      'status' => 'required|in:active,inactive'
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
