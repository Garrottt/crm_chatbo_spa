<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],

            'serviceId' => ['nullable', 'string', 'max:191'],
            'specialistId' => ['nullable', 'string', 'max:191'],
            'discountType' => ['required', Rule::in(['PERCENTAGE', 'FIXED_AMOUNT', 'CUSTOM_TEXT'])],
            'discountValue' => ['nullable', 'integer', 'min:0'],
            'customText' => ['nullable', 'string', 'max:255'],
            'startsAt' => ['required', 'date'],
            'endsAt' => ['required', 'date', 'after_or_equal:startsAt'],
            'maxRedemptions' => ['nullable', 'integer', 'min:1'],
            'active' => ['nullable', 'boolean'],
        ];
    }
}
