<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sale_number' => $this->sale_number,
            'customer_id' => $this->customer_id,
            'seller_id' => $this->seller_id,
            'sale_date' => $this->sale_date,
            'subtotal' => (float) $this->subtotal,
            'discount_total' => (float) $this->discount_total,
            'tax_total' => (float) $this->tax_total,
            'total' => (float) $this->total,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'estado' => $this->estado,
            'customer' => new UserResource($this->whenLoaded('customer')),
            'seller' => new UserResource($this->whenLoaded('seller')),
            'details' => SaleDetailResource::collection($this->whenLoaded('details')),
            'notes' => SaleNoteResource::collection($this->whenLoaded('notes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
