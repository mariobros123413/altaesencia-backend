<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class StorefrontController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_CATEGORIES = ['clothing', 'perfumes', 'cosmetics'];

    public function bootstrap(): JsonResponse
    {
        return response()->json([
            'brand' => config('storefront.brand'),
            'commerce' => config('storefront.commerce'),
            'categories' => array_values(config('storefront.categories', [])),
            'home' => config('storefront.home'),
        ]);
    }

    public function categoryProducts(string $categoryId): JsonResponse
    {
        if (! in_array($categoryId, self::ALLOWED_CATEGORIES, true)) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }

        $products = Product::query()
            ->with(['images' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('sort_order')])
            ->where('category', $categoryId)
            ->where('estado', 'activo')
            ->orderByDesc('is_promotional')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'description',
                'price',
                'original_price',
                'category',
                'image_url',
                'is_promotional',
                'discount_percentage',
                'rating',
            ]);

        return response()->json(
            $products->map(fn (Product $product) => $this->mapProduct($product))->values()
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function mapProduct(Product $product): array
    {
        return [
            'id' => (string) $product->id,
            'name' => (string) $product->name,
            'description' => (string) ($product->description ?? ''),
            'price' => (float) $product->price,
            'original_price' => $product->original_price !== null ? (float) $product->original_price : null,
            'category' => (string) $product->category,
            'image_url' => (string) $product->image_url,
            'image_urls' => $this->resolveProductImageUrls($product),
            'is_promotional' => (bool) $product->is_promotional,
            'discount_percentage' => (int) $product->discount_percentage,
            'rating' => (float) $product->rating,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function resolveProductImageUrls(Product $product): array
    {
        if ($product->relationLoaded('images')) {
            return $product->images
                ->pluck('image_url')
                ->filter()
                ->values()
                ->all();
        }

        return $product->image_url ? [$product->image_url] : [];
    }
}
