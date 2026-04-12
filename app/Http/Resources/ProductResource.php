<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'original_price' => $this->original_price !== null ? (float) $this->original_price : null,
            'category' => $this->category,
            'image_url' => $this->image_url,
            'is_promotional' => (bool) $this->is_promotional,
            'discount_percentage' => (int) $this->discount_percentage,
            'rating' => (float) $this->rating,
            'stock' => (int) $this->stock,
            'minimum_stock' => (int) $this->minimum_stock,
            'stock_status' => $this->stock <= 0 ? 'agotado' : ($this->stock <= $this->minimum_stock ? 'bajo' : 'disponible'),
            'estado' => $this->estado,
            'category_detail' => new CategoryResource($this->whenLoaded('categoryRelation')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
