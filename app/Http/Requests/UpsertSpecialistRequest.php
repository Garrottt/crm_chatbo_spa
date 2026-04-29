<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'availabilities.*.endTime' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ((array) $this->input('availabilities', []) as $day => $availability) {
                $enabled = filter_var($availability['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $startTime = $availability['startTime'] ?? null;
                $endTime = $availability['endTime'] ?? null;

                if (!$enabled) {
                    continue;
                }

                if (!$startTime || !$endTime) {
                    $validator->errors()->add("availabilities.$day.endTime", 'Debes indicar hora de inicio y termino para los dias activos.');
                    continue;
                }

                if ($endTime <= $startTime) {
                    $validator->errors()->add("availabilities.$day.endTime", 'La hora de termino debe ser posterior a la hora de inicio.');
                }
            }
        });
    }
}
