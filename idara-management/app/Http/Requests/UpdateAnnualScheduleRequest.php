<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnnualScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('schedule'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'planned_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'planned_month' => ['required', 'integer', 'min:1', 'max:12'],
            'status' => ['required', Rule::in(['pending', 'completed', 'skipped'])],
        ];
    }
}
