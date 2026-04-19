<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imageUrls = [];

        if ($this->relationLoaded('product') && $this->product) {
            if ($this->product->relationLoaded('images')) {
                $imageUrls = $this->product->images
                    ->pluck('image_url')
                    ->filter()
                    ->values()
                    ->all();
            } elseif ($this->product->image_url) {
                $imageUrls = [$this->product->image_url];
            }
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'movement_type' => $this->movement_type,
            'quantity' => (int) $this->quantity,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'notes' => $this->notes,
            'estado' => $this->estado,
            'image_urls' => $imageUrls,
            'product' => new ProductResource($this->whenLoaded('product')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
