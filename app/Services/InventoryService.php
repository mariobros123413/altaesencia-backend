<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function recordMovement(Product $product, array $attributes): InventoryMovement
    {
        return DB::transaction(function () use ($product, $attributes): InventoryMovement {
            $product = Product::query()->lockForUpdate()->findOrFail($product->id);
            $signedQuantity = $this->resolveSignedQuantity(
                $attributes['movement_type'],
                (int) $attributes['quantity']
            );

            $newStock = $product->stock + $signedQuantity;

            if ($newStock < 0) {
                throw ValidationException::withMessages([
                    'quantity' => ['Stock insuficiente para completar el movimiento.'],
                ]);
            }

            $product->update(['stock' => $newStock]);

            return InventoryMovement::query()->create([
                'product_id' => $product->id,
                'user_id' => $attributes['user_id'] ?? null,
                'movement_type' => $attributes['movement_type'],
                'quantity' => $signedQuantity,
                'reference_type' => $attributes['reference_type'] ?? null,
                'reference_id' => $attributes['reference_id'] ?? null,
                'notes' => $attributes['notes'] ?? null,
                'estado' => $attributes['estado'] ?? 'activo',
            ]);
        });
    }

    public function resolveSignedQuantity(string $movementType, int $quantity): int
    {
        return match ($movementType) {
            'entrada', 'devolucion' => abs($quantity),
            'salida', 'venta' => -1 * abs($quantity),
            'ajuste' => $quantity,
            default => throw ValidationException::withMessages([
                'movement_type' => ['Tipo de movimiento no soportado.'],
            ]),
        };
    }
}
