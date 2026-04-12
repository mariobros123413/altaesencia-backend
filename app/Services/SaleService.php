<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Sale
    {
        return DB::transaction(function () use ($attributes): Sale {
            $items = $attributes['items'];
            $notes = $attributes['notes'] ?? [];
            $extraDiscountTotal = (float) ($attributes['extra_discount_total'] ?? 0);
            $taxTotal = (float) ($attributes['tax_total'] ?? 0);

            $sale = Sale::query()->create([
                'sale_number' => $this->generateNextSaleNumber(),
                'customer_id' => $attributes['customer_id'] ?? null,
                'seller_id' => $attributes['seller_id'] ?? null,
                'sale_date' => $attributes['sale_date'] ?? now(),
                'subtotal' => 0,
                'discount_total' => 0,
                'tax_total' => $taxTotal,
                'total' => 0,
                'payment_method' => $attributes['payment_method'] ?? 'efectivo',
                'payment_status' => $attributes['payment_status'] ?? 'pagado',
                'estado' => $attributes['estado'] ?? 'activo',
            ]);

            $subtotal = 0.0;
            $discountTotal = $extraDiscountTotal;

            foreach ($items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) ($item['unit_price'] ?? $product->price);
                $discountAmount = (float) ($item['discount_amount'] ?? 0);
                $grossLineTotal = $unitPrice * $quantity;

                if ($discountAmount > $grossLineTotal) {
                    throw ValidationException::withMessages([
                        'items' => ['El descuento de un item no puede superar su subtotal.'],
                    ]);
                }

                $lineTotal = $grossLineTotal - $discountAmount;
                $subtotal += $grossLineTotal;
                $discountTotal += $discountAmount;

                $sale->details()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discountAmount,
                    'line_total' => round($lineTotal, 2),
                    'estado' => 'activo',
                ]);

                $this->inventoryService->recordMovement($product, [
                    'user_id' => $attributes['seller_id'] ?? null,
                    'movement_type' => 'venta',
                    'quantity' => $quantity,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'notes' => 'Salida de inventario por venta '.$sale->sale_number,
                    'estado' => 'activo',
                ]);
            }

            foreach ($notes as $note) {
                $sale->notes()->create([
                    'user_id' => $note['user_id'] ?? ($attributes['seller_id'] ?? null),
                    'note_type' => $note['note_type'] ?? 'interna',
                    'note' => $note['note'],
                    'estado' => 'activo',
                ]);
            }

            $total = round($subtotal - $discountTotal + $taxTotal, 2);

            $sale->update([
                'subtotal' => round($subtotal, 2),
                'discount_total' => round($discountTotal, 2),
                'total' => $total,
            ]);

            return $sale->fresh(['customer', 'seller', 'details.product', 'notes.user']);
        });
    }

    public function cancel(Sale $sale, ?int $userId = null, ?string $note = null): Sale
    {
        return DB::transaction(function () use ($sale, $userId, $note): Sale {
            $sale = Sale::query()->with(['details.product'])->lockForUpdate()->findOrFail($sale->id);

            if ($sale->payment_status === 'anulado' || $sale->estado === 'anulado') {
                throw ValidationException::withMessages([
                    'sale' => ['La venta ya fue anulada.'],
                ]);
            }

            foreach ($sale->details as $detail) {
                if (! $detail->product) {
                    continue;
                }

                $this->inventoryService->recordMovement($detail->product, [
                    'user_id' => $userId,
                    'movement_type' => 'devolucion',
                    'quantity' => $detail->quantity,
                    'reference_type' => 'sale_cancelled',
                    'reference_id' => $sale->id,
                    'notes' => 'Reversion de inventario por anulacion de venta '.$sale->sale_number,
                    'estado' => 'activo',
                ]);
            }

            $sale->update([
                'payment_status' => 'anulado',
                'estado' => 'anulado',
            ]);

            $sale->notes()->create([
                'user_id' => $userId,
                'note_type' => 'interna',
                'note' => $note ?: 'Venta anulada y stock reintegrado automaticamente.',
                'estado' => 'activo',
            ]);

            return $sale->fresh(['customer', 'seller', 'details.product', 'notes.user']);
        });
    }

    private function generateNextSaleNumber(): string
    {
        $latest = Sale::query()
            ->lockForUpdate()
            ->latest('created_at')
            ->value('sale_number');

        if (! $latest) {
            return 'VTA-000001';
        }

        $sequence = (int) substr($latest, 4);

        return 'VTA-'.str_pad((string) ($sequence + 1), 6, '0', STR_PAD_LEFT);
    }
}
