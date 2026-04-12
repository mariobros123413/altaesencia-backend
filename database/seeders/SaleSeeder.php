<?php

namespace Database\Seeders;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $customer = User::query()->where('user_type', 'cliente')->first();
        $seller = User::query()->where('user_type', 'administrativo')->first();
        $products = Product::query()->whereIn('sku', ['PERF-0001', 'COSM-0002'])->get()->keyBy('sku');

        if (! $customer || ! $seller || $products->count() < 2) {
            return;
        }

        $detailRows = [
            [
                'product' => $products['PERF-0001'],
                'quantity' => 1,
            ],
            [
                'product' => $products['COSM-0002'],
                'quantity' => 2,
            ],
        ];

        $subtotal = collect($detailRows)->sum(function (array $row) {
            return $row['product']->price * $row['quantity'];
        });

        $discountTotal = 10;
        $taxTotal = 0;
        $total = $subtotal - $discountTotal + $taxTotal;

        $sale = Sale::query()->create([
            'sale_number' => 'VTA-000001',
            'customer_id' => $customer->id,
            'seller_id' => $seller->id,
            'sale_date' => now(),
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'tax_total' => $taxTotal,
            'total' => $total,
            'payment_method' => 'qr',
            'payment_status' => 'pagado',
            'estado' => 'activo',
        ]);

        foreach ($detailRows as $row) {
            $product = $row['product'];
            $quantity = $row['quantity'];
            $lineTotal = ($product->price * $quantity);

            $sale->details()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'discount_amount' => 0,
                'line_total' => $lineTotal,
                'estado' => 'activo',
            ]);

            $product->decrement('stock', $quantity);

            InventoryMovement::query()->create([
                'product_id' => $product->id,
                'user_id' => $seller->id,
                'movement_type' => 'venta',
                'quantity' => -1 * $quantity,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'notes' => 'Descuento automatico por venta',
                'estado' => 'activo',
            ]);
        }

        $sale->notes()->create([
            'user_id' => $seller->id,
            'note_type' => 'interna',
            'note' => 'Venta demo generada para validar relaciones de ventas y stock.',
            'estado' => 'activo',
        ]);
    }
}
