<?php

namespace App\Domains\Products\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => 'required|min:4|max:100',
      'description' => 'required',
      'brand_id' => 'required|exists:brands,id',
      'status' => 'required|in:active,inactive',

      'images' => 'nullable|array|min:1|max:5',
      'images.*' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',

      'removed_images' => 'nullable|array',
      'removed_images.*' => 'nullable|integer|exists:product_images,id',

      'thumbnail_index' => 'required|integer'
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
