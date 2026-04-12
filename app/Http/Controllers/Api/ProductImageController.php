<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImageStoreRequest;
use App\Http\Requests\ProductImageUpdateRequest;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductImageController extends Controller
{
    public function store(ProductImageStoreRequest $request, Product $product): ProductImageResource
    {
        $image = DB::transaction(function () use ($request, $product): ProductImage {
            $data = $request->validated();

            if ($data['is_primary'] ?? false) {
                $product->images()->update(['is_primary' => false]);
            }

            return $product->images()->create([
                'image_url' => $data['image_url'],
                'is_primary' => $data['is_primary'] ?? ! $product->images()->exists(),
                'sort_order' => $data['sort_order'] ?? ((int) $product->images()->max('sort_order') + 1 ?: 1),
                'estado' => $data['estado'] ?? 'activo',
            ]);
        });

        return new ProductImageResource($image);
    }

    public function update(ProductImageUpdateRequest $request, Product $product, ProductImage $productImage): ProductImageResource
    {
        abort_unless($productImage->product_id === $product->id, 404);

        $image = DB::transaction(function () use ($request, $product, $productImage): ProductImage {
            $data = $request->validated();

            if (($data['is_primary'] ?? false) === true) {
                $product->images()->update(['is_primary' => false]);
            }

            $productImage->update($data);

            return $productImage->fresh();
        });

        return new ProductImageResource($image);
    }

    public function destroy(Product $product, ProductImage $productImage): JsonResponse
    {
        abort_unless($productImage->product_id === $product->id, 404);

        DB::transaction(function () use ($product, $productImage): void {
            $wasPrimary = $productImage->is_primary;

            $productImage->delete();

            if (! $wasPrimary) {
                return;
            }

            $nextImage = $product->images()->orderBy('sort_order')->first();

            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
                $product->update(['image_url' => $nextImage->image_url]);
            }
        });

        return response()->json([
            'message' => 'Imagen eliminada correctamente.',
        ]);
    }
}
