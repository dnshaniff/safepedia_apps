<?php

namespace App\Domains\WarehouseConstructions\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    $items = collect($this->input('item-budget', []))->map(function ($item) {
      return [
        'item_name' => $item['item_name'] ?? null,
        'quantity' => $item['quantity'] ?? null,
        'unit_price' => isset($item['unit_price'])
          ? str_replace('.', '', $item['unit_price'])
          : null,
      ];
    })->toArray();

    $this->merge(['item-budget' => $items]);
  }

  public function rules(): array
  {
    return [
      'warehouse_name' => 'required|min:4|max:100',

      'latitude' => 'required|numeric',
      'longitude' => 'required|numeric',

      'documents' => 'required|array|min:1|max:5',
      'documents.*' => 'required|file|mimes:pdf|max:2048',

      'item-budget' => 'required|array|min:1',

      'item-budget.*.item_name' => 'required|min:3|max:255',
      'item-budget.*.quantity' => 'required|integer|min:1',
      'item-budget.*.unit_price' => 'required|numeric|min:1',
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
