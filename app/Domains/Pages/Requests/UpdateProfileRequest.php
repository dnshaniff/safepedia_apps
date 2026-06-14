<?php

namespace App\Domains\Pages\Requests;

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
    $userId = $user?->id;

    return [
      'name' => 'required|min:4|max:50',
      'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($userId)],
      'username' => ['required', 'min:4', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
      'password' => ['nullable', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/', 'confirmed'],
      'two_factor_enabled' => 'required|boolean',
      'otp' => 'nullable|digits:6',
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
