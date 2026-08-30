<?php

namespace App\Http\Requests;

use App\Models\AnnualSchedule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnnualScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [AnnualSchedule::class, $this->route('department')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'planned_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'planned_month' => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }
}
