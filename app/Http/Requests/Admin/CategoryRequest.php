<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CategoryRequest extends FormRequest
{
    /**
     * Bestimmt, ob der Benutzer berechtigt ist, diese Anfrage zu stellen.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Ruft die Validierungsregeln ab, die auf die Anfrage angewendet werden.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name.en' => [
                'required',
                'string',
                'max:255',
            ],
            'name.*' => ['nullable', 'string', 'max:255'],

            'description.en' => ['nullable', 'string'],
            'description.*' => ['nullable', 'string'],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],

            'is_active' => ['sometimes', 'boolean'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * Bereitet die Daten für die Validierung vor.
     */
    protected function prepareForValidation(): void
    {
        if (empty($this->slug) && !empty($this->name['en'])) {
            $this->merge([
                'slug' => Str::slug($this->name['en']),
            ]);
        }

        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Ruft benutzerdefinierte Meldungen für Validierungsfehler ab.
     */
    public function messages(): array
    {
        return [
            'name.en.required' => 'Der Name in der Standardsprache (Englisch) ist erforderlich.',
            'slug.unique' => 'Dieser Slug ist bereits vergeben. Bitte wählen Sie einen einzigartigen Slug.',
            'parent_id.exists' => 'Die ausgewählte übergeordnete Kategorie existiert nicht.',
        ];
    }
}
