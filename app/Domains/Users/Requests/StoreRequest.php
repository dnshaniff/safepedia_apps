<?php

namespace App\Domains\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => 'required|min:4|max:50',
      'email' => 'required|email:rfc,dns|unique:users,email',
      'username' => 'required|min:4|max:30|unique:users,username',
      'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/|confirmed',
      'role' => 'required',
      'status' => 'required'
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
