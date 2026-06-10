<?php

namespace App\Http\Requests\Province;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProvinceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'postal_code' => ['required', 'string', 'max:10'],
            'contacts_email' => ['nullable', 'email', 'max:255'],
            'contacts_phone' => ['nullable', 'string', 'max:50'],
            'language_lingala' => ['nullable', 'boolean'],
            'language_kikongo' => ['nullable', 'boolean'],
            'language_kiluba' => ['nullable', 'boolean'],
            'language_kiswahili' => ['nullable', 'boolean'],
            'optional_languages' => ['nullable', 'array'],
            'optional_languages.*' => ['string', 'max:255'],
            'creation_date' => ['required', 'date'],
        ];
    }
}
