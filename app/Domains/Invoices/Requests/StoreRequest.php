<?php

namespace App\Domains\Invoices\Requests;

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
    $items = collect($this->input('item-invoice', []))
      ->map(function ($item) {
        return [
          'product_id' => $item['product_id'] ?? null,
          'quantity' => $item['quantity'] ?? null,
          'uom' => $item['uom'] ?? null,
          'unit_price' => isset($item['unit_price']) ? str_replace('.', '', $item['unit_price']) : null,
          'discount' => $item['discount'] ?? 0,
        ];
      })
      ->toArray();

    $this->merge(['item-invoice' => $items]);
  }

  public function rules(): array
  {
    return [
      'customer_name' => 'required|string|min:4|max:255',
      'customer_phone' => 'required|string|min:10|max:20',
      'customer_address' => 'required|string',
      'payment_terms' => 'required|in:cbd,cod,dp',
      'reference' => 'required|string|max:255',
      'issued_date' => 'required|date',
      'valid_until' => 'required|date|after_or_equal:issued_date',

      'item-invoice' => 'required|array|min:1',
      'item-invoice.*.product_id' => 'required|exists:products,id',
      'item-invoice.*.quantity' => 'required|numeric|min:1',
      'item-invoice.*.uom' => 'required|string|max:50',
      'item-invoice.*.unit_price' => 'required|numeric|min:0',
      'item-invoice.*.discount' => 'required|numeric|min:0|max:100',
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
