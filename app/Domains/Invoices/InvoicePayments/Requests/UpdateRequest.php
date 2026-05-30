<?php

namespace App\Domains\Invoices\InvoicePayments\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    $amount = $this->input('amount');

    $this->merge([
      'amount' => $amount ? str_replace('.', '', $amount) : null,
    ]);
  }

  public function rules(): array
  {
    return [
      'payment_date' => 'required|date',
      'amount' => 'required|numeric|min:0',
      'payment_method' => 'required|in:cash,bank_transfer',
      'file_upload' => 'nullable|file|mimes:jpg,jpeg,png|max:1024'
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    $errors = $validator->errors()->toArray();
    $message = collect($errors)->flatten()->implode("\n");

    throw new HttpResponseException(response()->json(['status' => 'danger', 'message' => $message, 'errors' => $errors], 422));
  }
}
