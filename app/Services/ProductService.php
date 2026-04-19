<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Product
    {
        return DB::transaction(function () use ($attributes): Product {
            $category = Category::query()->findOrFail($attributes['category_id']);

            $images = Arr::pull($attributes, 'images');
            $attributes['category'] = $category->legacy_key;
            $attributes = $this->normalizePricingAttributes($attributes);

            if (empty($attributes['image_url']) && empty($images)) {
                throw ValidationException::withMessages([
                    'image_url' => ['Debes enviar una imagen principal o un arreglo de imagenes.'],
                ]);
            }

            $attributes['image_url'] = $attributes['image_url'] ?? $this->resolvePrimaryImageUrl($images);

            $product = Product::query()->create($attributes);

            $this->syncImages($product, $images);

            return $product->load(['categoryRelation', 'images']);
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Product $product, array $attributes): Product
    {
        return DB::transaction(function () use ($product, $attributes): Product {
            $images = Arr::pull($attributes, 'images');

            if (array_key_exists('category_id', $attributes)) {
                $category = Category::query()->findOrFail($attributes['category_id']);
                $attributes['category'] = $category->legacy_key;
            }

            $attributes = $this->normalizePricingAttributes($attributes, $product);

            if ($images !== null) {
                $attributes['image_url'] = $attributes['image_url'] ?? $this->resolvePrimaryImageUrl($images, $product->image_url);
            }

            $product->update($attributes);

            if ($images !== null) {
                $this->syncImages($product, $images, true);
            } elseif (array_key_exists('image_url', $attributes)) {
                $primaryImage = $product->images()->where('is_primary', true)->first();

                if ($primaryImage) {
                    $primaryImage->update(['image_url' => $attributes['image_url']]);
                } else {
                    $product->images()->create([
                        'image_url' => $attributes['image_url'],
                        'is_primary' => true,
                        'sort_order' => 1,
                        'estado' => 'activo',
                    ]);
                }
            }

            return $product->fresh(['categoryRelation', 'images']);
        });
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $images
     */
    private function syncImages(Product $product, ?array $images, bool $replace = false): void
    {
        if ($images === null) {
            if (! $product->images()->exists()) {
                $product->images()->create([
                    'image_url' => $product->image_url,
                    'is_primary' => true,
                    'sort_order' => 1,
                    'estado' => 'activo',
                ]);
            }

            return;
        }

        if ($replace) {
            $product->images()->delete();
        }

        $normalizedImages = collect($images)
            ->map(function (array $image, int $index) {
                return [
                    'image_url' => $image['image_url'],
                    'is_primary' => (bool) ($image['is_primary'] ?? false),
                    'sort_order' => $image['sort_order'] ?? ($index + 1),
                    'estado' => $image['estado'] ?? 'activo',
                ];
            })
            ->values();

        if ($normalizedImages->every(fn (array $image) => ! $image['is_primary'])) {
            $normalizedImages[0]['is_primary'] = true;
        }

        $product->images()->createMany($normalizedImages->all());

        $primaryImage = $product->images()->where('is_primary', true)->orderBy('sort_order')->first();

        if ($primaryImage && $product->image_url !== $primaryImage->image_url) {
            $product->update(['image_url' => $primaryImage->image_url]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $images
     */
    private function resolvePrimaryImageUrl(?array $images, ?string $fallback = null): string
    {
        if (! empty($images)) {
            foreach ($images as $image) {
                if (($image['is_primary'] ?? false) === true) {
                    return $image['image_url'];
                }
            }

            return $images[0]['image_url'];
        }

        if ($fallback !== null) {
            return $fallback;
        }

        throw ValidationException::withMessages([
            'images' => ['No se pudo resolver una imagen principal para el producto.'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function normalizePricingAttributes(array $attributes, ?Product $product = null): array
    {
        $price = array_key_exists('price', $attributes)
            ? (float) $attributes['price']
            : ($product ? (float) $product->price : 0.0);

        $originalPrice = array_key_exists('original_price', $attributes)
            ? ($attributes['original_price'] !== null && $attributes['original_price'] !== '' ? (float) $attributes['original_price'] : null)
            : ($product?->original_price !== null ? (float) $product->original_price : null);

        $attributes['discount_percentage'] = $this->calculateDiscountPercentage($price, $originalPrice);

        return $attributes;
    }

    private function calculateDiscountPercentage(float $price, ?float $originalPrice): int
    {
        if ($originalPrice === null || $originalPrice <= 0 || $price >= $originalPrice) {
            return 0;
        }

        return (int) round((($originalPrice - $price) / $originalPrice) * 100);
    }
}
