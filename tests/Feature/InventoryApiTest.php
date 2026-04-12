<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products_from_the_api(): void
    {
        $category = Category::query()->create([
            'name' => 'Perfumes',
            'slug' => 'perfumes',
            'legacy_key' => 'perfumes',
            'estado' => 'activo',
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'sku' => 'PERF-1000',
            'name' => 'Perfume Test',
            'price' => 99.90,
            'category' => 'perfumes',
            'image_url' => 'https://example.com/perfume.jpg',
            'stock' => 5,
            'minimum_stock' => 1,
            'estado' => 'activo',
        ]);

        $response = $this->getJson('/api/products');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'sku' => 'PERF-1000',
                'name' => 'Perfume Test',
            ]);
    }

    public function test_it_registers_manual_inventory_movements_and_updates_stock(): void
    {
        $category = Category::query()->create([
            'name' => 'Cosmeticos',
            'slug' => 'cosmeticos',
            'legacy_key' => 'cosmetics',
            'estado' => 'activo',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'sku' => 'COSM-1000',
            'name' => 'Serum Test',
            'price' => 49.90,
            'category' => 'cosmetics',
            'image_url' => 'https://example.com/serum.jpg',
            'stock' => 5,
            'minimum_stock' => 1,
            'estado' => 'activo',
        ]);

        $user = User::factory()->create([
            'user_type' => 'administrativo',
        ]);

        $response = $this->postJson('/api/inventory-movements', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'movement_type' => 'entrada',
            'quantity' => 3,
            'notes' => 'Ingreso manual',
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'movement_type' => 'entrada',
                'quantity' => 3,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
    }

    public function test_it_creates_a_sale_and_discount_stock(): void
    {
        $category = Category::query()->create([
            'name' => 'Ropa',
            'slug' => 'ropa',
            'legacy_key' => 'clothing',
            'estado' => 'activo',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'sku' => 'ROPA-1000',
            'name' => 'Chaqueta Test',
            'price' => 120.00,
            'category' => 'clothing',
            'image_url' => 'https://example.com/chaqueta.jpg',
            'stock' => 10,
            'minimum_stock' => 2,
            'estado' => 'activo',
        ]);

        $customer = User::factory()->create([
            'user_type' => 'cliente',
        ]);

        $seller = User::factory()->create([
            'user_type' => 'administrativo',
        ]);

        $response = $this->postJson('/api/sales', [
            'customer_id' => $customer->id,
            'seller_id' => $seller->id,
            'payment_method' => 'efectivo',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'discount_amount' => 10,
                ],
            ],
            'notes' => [
                [
                    'user_id' => $seller->id,
                    'note_type' => 'interna',
                    'note' => 'Venta creada desde prueba',
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'product_name' => 'Chaqueta Test',
                'quantity' => 2,
                'payment_method' => 'efectivo',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'movement_type' => 'venta',
            'quantity' => -2,
        ]);
    }
}
