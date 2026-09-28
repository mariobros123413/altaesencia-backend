<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCosmeticsCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_cosmetics_catalog_page_is_public_and_uses_the_storefront_assets(): void
    {
        $this->get('/catalog/cosmetics')
            ->assertOk()
            ->assertSee('Cosmetics Catalog')
            ->assertSee('/catalog/cosmetics/catalog.js', false)
            ->assertSee('/catalog/cosmetics/catalog.css', false);
    }

    public function test_public_storefront_returns_only_active_cosmetics_with_catalog_fields(): void
    {
        $cosmetics = $this->category('Cosmetics', 'cosmetics');
        $perfumes = $this->category('Perfumes', 'perfumes');

        $product = $this->product($cosmetics, [
            'name' => 'Radiance Serum',
            'category' => 'cosmetics',
            'image_url' => 'https://example.com/serum.jpg',
            'is_promotional' => true,
            'original_price' => 90.00,
            'price' => 72.00,
            'discount_percentage' => 20,
            'rating' => 4.7,
        ]);
        $this->product($perfumes, ['category' => 'perfumes']);
        $this->product($cosmetics, ['estado' => 'inactivo']);

        $this->getJson('/storefront/categories/cosmetics/products')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $product->id)
            ->assertJsonPath('0.category', 'cosmetics')
            ->assertJsonStructure([[
                'id', 'name', 'description', 'price', 'original_price', 'category',
                'image_url', 'image_urls', 'is_promotional', 'discount_percentage', 'rating',
            ]]);
    }

    private function category(string $name, string $legacyKey): Category
    {
        return Category::query()->create([
            'name' => $name,
            'slug' => $legacyKey,
            'legacy_key' => $legacyKey,
            'estado' => 'activo',
        ]);
    }

    /** @param array<string, mixed> $attributes */
    private function product(Category $category, array $attributes = []): Product
    {
        return Product::query()->create(array_merge([
            'category_id' => $category->id,
            'sku' => 'SKU-'.fake()->unique()->numerify('####'),
            'name' => 'Test product',
            'description' => 'Test description',
            'price' => 50.00,
            'category' => $category->legacy_key,
            'image_url' => 'https://example.com/product.jpg',
            'is_promotional' => false,
            'discount_percentage' => 0,
            'rating' => 4.0,
            'stock' => 1,
            'minimum_stock' => 0,
            'estado' => 'activo',
        ], $attributes));
    }
}
