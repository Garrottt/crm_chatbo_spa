<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'objective' => ['required', 'string', 'max:120'],
            'offerId' => ['required', 'string', 'max:191'],
            'segmentType' => ['required', Rule::in(['inactive_30', 'inactive_90', 'frequent', 'consulted_no_booking'])],
            'messageTemplate' => ['required', 'string', 'max:5000'],
            'minBookings' => ['nullable', 'integer', 'min:1'],
            'lookbackDays' => ['nullable', 'integer', 'min:1', 'max:365'],
            'ignoreCooldown' => ['nullable', 'boolean'],
        ];
    }
}
