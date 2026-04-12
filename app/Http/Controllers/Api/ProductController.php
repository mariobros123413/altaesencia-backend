<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    public function index(Request $request)
    {
        $sortBy = in_array($request->string('sort_by')->toString(), ['created_at', 'name', 'price', 'stock', 'rating'], true)
            ? $request->string('sort_by')->toString()
            : 'created_at';
        $sortDirection = $request->string('sort_direction')->toString() === 'asc' ? 'asc' : 'desc';

        $products = Product::query()
            ->with(['categoryRelation', 'images'])
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->string('category_id')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->when($request->has('is_promotional'), fn ($query) => $query->where('is_promotional', $request->boolean('is_promotional')))
            ->when($request->boolean('low_stock'), fn ($query) => $query->whereColumn('stock', '<=', 'minimum_stock'))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search');
                $query->where(function ($innerQuery) use ($term) {
                    $innerQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return ProductResource::collection($products);
    }

    public function store(ProductStoreRequest $request): ProductResource
    {
        return new ProductResource($this->productService->create($request->validated()));
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load(['categoryRelation', 'images']));
    }

    public function update(ProductUpdateRequest $request, Product $product): ProductResource
    {
        return new ProductResource($this->productService->update($product, $request->validated()));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->update(['estado' => 'inactivo']);

        return response()->json([
            'message' => 'Producto desactivado correctamente.',
            'data' => new ProductResource($product->fresh(['categoryRelation', 'images'])),
        ]);
    }
}
