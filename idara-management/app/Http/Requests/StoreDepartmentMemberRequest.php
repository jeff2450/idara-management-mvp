<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Kuongeza mtu kwenye idara - ama mtumiaji aliyepo tayari (kwa email) au
 * mtumiaji mpya (jina/email/simu/nenosiri). Kiongozi anaweza kuongeza
 * "member" pekee; "leader" ni Admin pekee - angalia DepartmentPolicy::assignLeader().
 */
class StoreDepartmentMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageMembers', $this->route('department'));
    }

    public function rules(): array
    {
        $rules = [
            'mode' => ['required', Rule::in(['existing', 'new'])],
            'role' => ['required', Rule::in(['leader', 'member'])],
        ];

        if ($this->input('mode') === 'existing') {
            $rules['email'] = ['required', 'email', 'exists:users,email'];
        } else {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'unique:users,email'];
            $rules['phone'] = ['nullable', 'string', 'max:20'];
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('role') === 'leader' && ! $this->user()->isAdmin()) {
                $validator->errors()->add('role', 'Kiongozi wa idara pekee huteuliwa na Admin.');
            }
        });
    }
}
