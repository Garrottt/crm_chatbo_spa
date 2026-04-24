<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertSpecialistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $specialist = $this->route('specialist');
        $userId = $specialist?->user?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                $specialist ? 'nullable' : 'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id'),
            ],
            'password' => [$specialist ? 'nullable' : 'required', 'string', 'min:8'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
            'serviceIds' => ['nullable', 'array'],
            'serviceIds.*' => ['string', 'exists:Service,id'],
            'availabilities' => ['nullable', 'array'],
            'availabilities.*.enabled' => ['nullable', 'boolean'],
            'availabilities.*.startTime' => ['nullable', 'date_format:H:i'],
            'availabilities.*.endTime' => ['nullable', 'date_format:H:i', 'after:availabilities.*.startTime'],
        ];
    }
}
