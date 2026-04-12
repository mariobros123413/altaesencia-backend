<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleNoteStoreRequest extends FormRequest
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
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'note_type' => ['sometimes', 'in:interna,cliente'],
            'note' => ['required', 'string'],
            'estado' => ['sometimes', 'string', 'max:30'],
        ];
    }
}
