<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryMovementStoreRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'movement_type' => ['required', 'in:entrada,salida,ajuste,venta,devolucion'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'reference_type' => ['nullable', 'string', 'max:50'],
            'reference_id' => ['nullable', 'uuid'],
            'notes' => ['nullable', 'string'],
            'estado' => ['sometimes', 'string', 'max:30'],
        ];
    }
}
