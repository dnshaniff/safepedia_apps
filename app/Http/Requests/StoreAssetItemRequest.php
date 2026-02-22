<?php

namespace App\Http\Requests;

use App\Models\AssetItem;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAssetItemRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'asset_type_id' => 'required|exists:asset_types,id',
      'item_brand' => 'required|string',
      'serial_number' => 'nullable|string|unique:asset_items,serial_number',
      'item_model' => 'nullable|string',
      'item_specification' => 'required|string',
      'company_id' => 'required|exists:companies,id',
      'item_status' => ['required', Rule::in(AssetItem::STATUSES)],
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
