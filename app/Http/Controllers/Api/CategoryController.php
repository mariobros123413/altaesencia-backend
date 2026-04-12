<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search');
                $query->where(function ($innerQuery) use ($term) {
                    $innerQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%")
                        ->orWhere('legacy_key', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return CategoryResource::collection($categories);
    }

    public function store(CategoryRequest $request): CategoryResource
    {
        $data = $request->validated();
        $name = $data['name'];

        $category = Category::query()->create([
            'name' => $name,
            'slug' => $data['slug'] ?? Str::slug($name),
            'legacy_key' => $data['legacy_key'] ?? Str::snake($name),
            'description' => $data['description'] ?? null,
            'estado' => $data['estado'] ?? 'activo',
        ]);

        return new CategoryResource($category->loadCount('products'));
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category->loadCount('products'));
    }

    public function update(CategoryRequest $request, Category $category): CategoryResource
    {
        $data = $request->validated();

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['name']) && ! isset($data['legacy_key'])) {
            $data['legacy_key'] = Str::snake($data['name']);
        }

        $category->update($data);

        return new CategoryResource($category->fresh()->loadCount('products'));
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->update(['estado' => 'inactivo']);

        return response()->json([
            'message' => 'Categoria desactivada correctamente.',
            'data' => new CategoryResource($category->fresh()->loadCount('products')),
        ]);
    }
}
