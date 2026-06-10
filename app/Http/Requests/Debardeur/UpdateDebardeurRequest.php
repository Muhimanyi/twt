<?php

namespace App\Http\Requests\Debardeur;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDebardeurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'province_id' => ['required', 'exists:provinces,id'],
            'sector' => ['required', 'string', 'in:Routier,Lacustre,Aérien,Ferroviaire'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'birth_place' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['required', 'string', 'in:M,F'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'marital_status' => ['required', 'string', 'max:255'],
            'nationality' => ['required', 'string', 'max:255'],
            'id_type' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:255'],
            'id_expiration_date' => ['nullable', 'date'],
            'migratory_doc_type' => ['nullable', 'string', 'max:255'],
            'migratory_doc_number' => ['nullable', 'string', 'max:255'],
            'profession' => ['required', 'string', 'max:255'],
            'assignment_place' => ['required', 'string', 'max:255'],
            'association_cooperative' => ['nullable', 'string', 'max:255'],
            'member_card_number' => ['nullable', 'string', 'max:255'],
            'vest_number' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'residence_address' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ];
    }
}
