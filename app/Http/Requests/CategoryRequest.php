<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'legacy_key' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'legacy_key')->ignore($categoryId)],
            'description' => ['nullable', 'string'],
            'estado' => ['sometimes', 'string', 'max:30'],
        ];
    }
}
