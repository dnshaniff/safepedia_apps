<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeAgreementRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'agreement_type' => [
        'required',
        Rule::in(['Contract', 'Conversion', 'Extension', 'Promotion', 'Resignation', 'Warning']),
      ],
      'start_date' => [
        'nullable',
        'date',
        Rule::requiredIf(
          fn() =>
          in_array($this->agreement_type, ['Contract', 'Extension'], true)
        ),
      ],
      'end_date' => [
        'nullable',
        'date',
        'after_or_equal:start_date',
        Rule::requiredIf(
          fn() =>
          in_array($this->agreement_type, ['Contract', 'Extension'], true)
        ),
      ],
      'effective_date' => [
        'nullable',
        'date',
        Rule::requiredIf(
          fn() =>
          !in_array($this->agreement_type, ['Contract', 'Extension'], true)
        ),
      ],
      'notes' => ['nullable', 'string'],
    ];
  }
}
