<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleCancelRequest;
use App\Http\Requests\SalePaymentStatusRequest;
use App\Http\Requests\SaleStoreRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService
    ) {
    }

    public function index(Request $request)
    {
        $sales = Sale::query()
            ->with(['customer', 'seller'])
            ->when($request->filled('customer_id'), fn ($query) => $query->where('customer_id', $request->integer('customer_id')))
            ->when($request->filled('seller_id'), fn ($query) => $query->where('seller_id', $request->integer('seller_id')))
            ->when($request->filled('payment_status'), fn ($query) => $query->where('payment_status', $request->string('payment_status')))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('sale_date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('sale_date', '<=', $request->date('date_to')))
            ->latest('sale_date')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return SaleResource::collection($sales);
    }

    public function store(SaleStoreRequest $request): SaleResource
    {
        return new SaleResource($this->saleService->create($request->validated()));
    }

    public function show(Sale $sale): SaleResource
    {
        return new SaleResource($sale->load(['customer', 'seller', 'details.product.categoryRelation', 'notes.user']));
    }

    public function updatePaymentStatus(SalePaymentStatusRequest $request, Sale $sale): JsonResponse
    {
        $sale->update($request->validated());

        return response()->json([
            'message' => 'Estado de pago actualizado correctamente.',
            'data' => new SaleResource($sale->fresh(['customer', 'seller', 'details.product', 'notes.user'])),
        ]);
    }

    public function cancel(SaleCancelRequest $request, Sale $sale): JsonResponse
    {
        $sale = $this->saleService->cancel(
            $sale,
            $request->validated('user_id'),
            $request->validated('note')
        );

        return response()->json([
            'message' => 'Venta anulada correctamente.',
            'data' => new SaleResource($sale),
        ]);
    }
}
