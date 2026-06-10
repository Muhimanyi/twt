<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'role' => ['required', 'array'],
            'role.*' => ['string'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'province_assignments' => ['nullable', 'array'],
            'province_assignments.*.province_id' => ['required_with:province_assignments', 'exists:provinces,id', 'distinct'],
            'province_assignments.*.role' => ['required_with:province_assignments', 'string'],
            'province_assignments.*.is_primary' => ['nullable', 'boolean'],
        ];
    }
}
