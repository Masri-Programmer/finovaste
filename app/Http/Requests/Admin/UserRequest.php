<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * In einem Admin-Bereich sollte dies normalerweise 'true' sein
     * oder eine Berechtigungsprüfung (z.B. can('manage users')) beinhalten.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ];

        // Beim Erstellen eines neuen Benutzers muss das Passwort zwingend angegeben werden.
        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    /**
     * Custom error messages for validation.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Der Name ist erforderlich.',
            'name.max' => 'Der Name darf maximal 255 Zeichen lang sein.',
            'email.required' => 'Die E-Mail-Adresse ist erforderlich.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.',
            'email.unique' => 'Diese E-Mail-Adresse ist bereits registriert.',
            'password.required' => 'Ein Passwort ist beim Erstellen eines Benutzers erforderlich.',
            'password.confirmed' => 'Die Passwortbestätigung stimmt nicht überein.',
            'roles.array' => 'Rollen müssen als Liste übergeben werden.',
            'roles.*.exists' => 'Mindestens eine ausgewählte Rolle existiert nicht.',
        ];
    }
}
