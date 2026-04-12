<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleStoreRequest extends FormRequest
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
            'customer_id' => ['nullable', 'exists:users,id'],
            'seller_id' => ['nullable', 'exists:users,id'],
            'sale_date' => ['nullable', 'date'],
            'payment_method' => ['sometimes', 'in:efectivo,tarjeta,transferencia,qr,mixto'],
            'payment_status' => ['sometimes', 'in:pendiente,pagado,anulado'],
            'tax_total' => ['sometimes', 'numeric', 'min:0'],
            'extra_discount_total' => ['sometimes', 'numeric', 'min:0'],
            'estado' => ['sometimes', 'string', 'max:30'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'notes' => ['sometimes', 'array'],
            'notes.*.user_id' => ['nullable', 'exists:users,id'],
            'notes.*.note_type' => ['sometimes', 'in:interna,cliente'],
            'notes.*.note' => ['required_with:notes', 'string'],
        ];
    }
}
