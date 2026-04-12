<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'sku' => ['sometimes', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_promotional' => ['sometimes', 'boolean'],
            'discount_percentage' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'rating' => ['sometimes', 'numeric', 'min:0', 'max:5'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'minimum_stock' => ['sometimes', 'integer', 'min:0'],
            'estado' => ['sometimes', 'string', 'max:30'],
            'images' => ['sometimes', 'array', 'min:1'],
            'images.*.image_url' => ['required_with:images', 'url', 'max:2048'],
            'images.*.is_primary' => ['sometimes', 'boolean'],
            'images.*.sort_order' => ['sometimes', 'integer', 'min:1'],
            'images.*.estado' => ['sometimes', 'string', 'max:30'],
        ];
    }
}
