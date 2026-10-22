<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function cosmetics(): View
    {
        return view('catalog.cosmetics');
    }

    public function show(Request $request): View
    {
        $validated = $request->validate([
            'products' => ['required', 'string', 'max:8000'],
            'title' => ['nullable', 'string', 'max:100'],
            'fields' => ['nullable', 'string', 'max:300'],
        ]);

        $ids = array_values(array_unique(array_filter(explode(',', $validated['products']))));
        abort_if(count($ids) === 0 || count($ids) > 100, 404);
        $products = Product::query()->with(['categoryRelation', 'images'])
            ->where('estado', 'activo')->whereIn('id', $ids)->get()
            ->sortBy(fn (Product $product) => array_search($product->id, $ids, true))->values();
        abort_if($products->isEmpty(), 404);

        $allowedFields = ['image', 'name', 'description', 'price', 'category', 'sku', 'rating', 'promotion'];
        $fields = array_values(array_intersect(explode(',', $validated['fields'] ?? 'name,description,price,category'), $allowedFields));

        $catalogProducts = $products->map(fn (Product $product) => [
            'code' => $product->sku,
            'tag' => $product->categoryRelation?->name ?? $product->category ?? 'Producto',
            'brand' => $product->categoryRelation?->name ?? 'AltaEsencia',
            'name' => $product->name,
            'price' => (float) $product->price,
            'originalPrice' => $product->original_price !== null ? (float) $product->original_price : null,
            'category' => $product->categoryRelation?->name ?? $product->category ?? '',
            'rating' => (float) $product->rating,
            'promotion' => $product->is_promotional ? (int) $product->discount_percentage : 0,
            'size' => '',
            'image' => $product->image_url,
            'description' => $product->description ?? '',
        ])->all();

        return view('catalog.designed', [
            'products' => $products,
            'catalogProducts' => $catalogProducts,
            'title' => $validated['title'] ?? 'Catálogo AltaEsencia',
            'fields' => $fields,
        ]);
    }
}
