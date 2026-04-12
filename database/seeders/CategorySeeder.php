<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ropa',
                'slug' => 'ropa',
                'legacy_key' => 'clothing',
                'description' => 'Prendas premium y accesorios de vestir.',
                'estado' => 'activo',
            ],
            [
                'name' => 'Perfumes',
                'slug' => 'perfumes',
                'legacy_key' => 'perfumes',
                'description' => 'Fragancias exclusivas para uso diario y ocasiones especiales.',
                'estado' => 'activo',
            ],
            [
                'name' => 'Cosmeticos',
                'slug' => 'cosmeticos',
                'legacy_key' => 'cosmetics',
                'description' => 'Productos de cuidado personal y belleza.',
                'estado' => 'activo',
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->create($category);
        }
    }
}
