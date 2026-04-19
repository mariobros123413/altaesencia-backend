<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryMovementStoreRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {
    }

    public function index(Request $request)
    {
        $movements = InventoryMovement::query()
            ->with(['product.categoryRelation', 'product.images', 'user'])
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->string('product_id')))
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
            ->when($request->filled('movement_type'), fn ($query) => $query->where('movement_type', $request->string('movement_type')))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('date_to')))
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return InventoryMovementResource::collection($movements);
    }

    public function byProduct(Request $request, Product $product)
    {
        $movements = $product->inventoryMovements()
            ->with(['product.categoryRelation', 'product.images', 'user'])
            ->when($request->filled('movement_type'), fn ($query) => $query->where('movement_type', $request->string('movement_type')))
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return InventoryMovementResource::collection($movements);
    }

    public function store(InventoryMovementStoreRequest $request): InventoryMovementResource
    {
        $data = $request->validated();
        $product = Product::query()->findOrFail($data['product_id']);

        return new InventoryMovementResource(
            $this->inventoryService->recordMovement($product, $data)->load(['product.categoryRelation', 'product.images', 'user'])
        );
    }
}
