<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = Category::query()->get()->keyBy('legacy_key');

        $products = [
            [
                'name' => 'Perfume AltaEsencia Negro',
                'description' => 'Fragancia exclusiva con notas de ambar y vainilla',
                'price' => 189.99,
                'original_price' => 249.99,
                'category' => 'perfumes',
                'image_url' => 'https://images.pexels.com/photos/3962286/pexels-photo-3962286.jpeg',
                'is_promotional' => true,
                'discount_percentage' => 24,
                'rating' => 4.9,
                'stock' => 20,
                'minimum_stock' => 5,
                'sku' => 'PERF-0001',
            ],
            [
                'name' => 'Chaqueta Premium Negra',
                'description' => 'Chaqueta de lujo en tela 100% algodon',
                'price' => 399.99,
                'original_price' => 499.99,
                'category' => 'clothing',
                'image_url' => 'https://images.pexels.com/photos/3622622/pexels-photo-3622622.jpeg',
                'is_promotional' => true,
                'discount_percentage' => 20,
                'rating' => 4.8,
                'stock' => 12,
                'minimum_stock' => 3,
                'sku' => 'ROPA-0001',
            ],
            [
                'name' => 'Serum Facial Dorado',
                'description' => 'Serum antienvejecimiento con oro coloidal',
                'price' => 129.99,
                'original_price' => 179.99,
                'category' => 'cosmetics',
                'image_url' => 'https://images.pexels.com/photos/3762285/pexels-photo-3762285.jpeg',
                'is_promotional' => false,
                'discount_percentage' => 0,
                'rating' => 4.7,
                'stock' => 18,
                'minimum_stock' => 4,
                'sku' => 'COSM-0001',
            ],
            [
                'name' => 'Tom Ford Noir',
                'description' => 'Perfume de lujo Tom Ford Negro',
                'price' => 249.99,
                'original_price' => 349.99,
                'category' => 'perfumes',
                'image_url' => 'https://images.pexels.com/photos/3962286/pexels-photo-3962286.jpeg',
                'is_promotional' => false,
                'discount_percentage' => 0,
                'rating' => 5.0,
                'stock' => 10,
                'minimum_stock' => 2,
                'sku' => 'PERF-0002',
            ],
            [
                'name' => 'Pantalon Premium Gris',
                'description' => 'Pantalon de vestir en lana fina italiana',
                'price' => 279.99,
                'original_price' => 349.99,
                'category' => 'clothing',
                'image_url' => 'https://images.pexels.com/photos/3622622/pexels-photo-3622622.jpeg',
                'is_promotional' => false,
                'discount_percentage' => 0,
                'rating' => 4.6,
                'stock' => 14,
                'minimum_stock' => 4,
                'sku' => 'ROPA-0002',
            ],
            [
                'name' => 'Lipstick Rojo Intenso',
                'description' => 'Labial de larga duracion en rojo profundo',
                'price' => 79.99,
                'original_price' => 99.99,
                'category' => 'cosmetics',
                'image_url' => 'https://images.pexels.com/photos/3987003/pexels-photo-3987003.jpeg',
                'is_promotional' => true,
                'discount_percentage' => 20,
                'rating' => 4.8,
                'stock' => 30,
                'minimum_stock' => 8,
                'sku' => 'COSM-0002',
            ],
            [
                'name' => 'Dior Sauvage',
                'description' => 'Perfume fresco y sofisticado de Dior',
                'price' => 199.99,
                'original_price' => 279.99,
                'category' => 'perfumes',
                'image_url' => 'https://images.pexels.com/photos/3962286/pexels-photo-3962286.jpeg',
                'is_promotional' => false,
                'discount_percentage' => 0,
                'rating' => 4.9,
                'stock' => 16,
                'minimum_stock' => 4,
                'sku' => 'PERF-0003',
            ],
            [
                'name' => 'Sueter de Cachemira',
                'description' => 'Sueter premium en cachemira pura',
                'price' => 359.99,
                'original_price' => 459.99,
                'category' => 'clothing',
                'image_url' => 'https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg',
                'is_promotional' => false,
                'discount_percentage' => 0,
                'rating' => 4.7,
                'stock' => 9,
                'minimum_stock' => 2,
                'sku' => 'ROPA-0003',
            ],
            [
                'name' => 'Crema Hidratante Luxury',
                'description' => 'Crema facial con ingredientes premium',
                'price' => 149.99,
                'original_price' => 199.99,
                'category' => 'cosmetics',
                'image_url' => 'https://images.pexels.com/photos/3738313/pexels-photo-3738313.jpeg',
                'is_promotional' => false,
                'discount_percentage' => 0,
                'rating' => 4.8,
                'stock' => 22,
                'minimum_stock' => 6,
                'sku' => 'COSM-0003',
            ],
            [
                'name' => 'Perfume AltaEsencia Oro',
                'description' => 'Fragancia dorada con notas florales',
                'price' => 219.99,
                'original_price' => 299.99,
                'category' => 'perfumes',
                'image_url' => 'https://images.pexels.com/photos/3962286/pexels-photo-3962286.jpeg',
                'is_promotional' => true,
                'discount_percentage' => 26,
                'rating' => 5.0,
                'stock' => 15,
                'minimum_stock' => 4,
                'sku' => 'PERF-0004',
            ],
        ];

        foreach ($products as $data) {
            $product = Product::query()->create([
                'category_id' => $categories[$data['category']]->id,
                'sku' => $data['sku'],
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'original_price' => $data['original_price'],
                'category' => $data['category'],
                'image_url' => $data['image_url'],
                'is_promotional' => $data['is_promotional'],
                'discount_percentage' => $data['discount_percentage'],
                'rating' => $data['rating'],
                'stock' => $data['stock'],
                'minimum_stock' => $data['minimum_stock'],
                'estado' => 'activo',
            ]);

            $product->images()->createMany([
                [
                    'image_url' => $data['image_url'],
                    'image_url_hash' => hash('sha256', $data['image_url']),
                    'is_primary' => true,
                    'sort_order' => 1,
                    'estado' => 'activo',
                ],
                [
                    'image_url' => $data['image_url'].'?variant=2',
                    'image_url_hash' => hash('sha256', $data['image_url'].'?variant=2'),
                    'is_primary' => false,
                    'sort_order' => 2,
                    'estado' => 'activo',
                ],
            ]);

            InventoryMovement::query()->create([
                'product_id' => $product->id,
                'movement_type' => 'entrada',
                'quantity' => $data['stock'],
                'reference_type' => 'stock_inicial',
                'notes' => 'Carga inicial del producto',
                'estado' => 'activo',
            ]);
        }
    }
}
