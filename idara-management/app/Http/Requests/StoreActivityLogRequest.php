<?php

namespace App\Http\Requests;

use App\Models\ActivityLog;
use Illuminate\Foundation\Http\FormRequest;

class StoreActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [ActivityLog::class, $this->route('department')]);
    }

    public function rules(): array
    {
        return [
            'annual_schedule_id' => ['nullable', 'exists:annual_schedules,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'occurred_at' => ['required', 'date'],
        ];
    }
}
