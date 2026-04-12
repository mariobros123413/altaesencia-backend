<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $todaySales = Sale::query()->whereDate('sale_date', today());

        return response()->json([
            'summary' => [
                'products_total' => Product::query()->count(),
                'products_low_stock' => Product::query()->whereColumn('stock', '<=', 'minimum_stock')->count(),
                'customers_total' => User::query()->where('user_type', 'cliente')->count(),
                'administrativos_total' => User::query()->where('user_type', 'administrativo')->count(),
                'sales_total' => Sale::query()->count(),
                'sales_today' => $todaySales->count(),
                'sales_today_amount' => (float) $todaySales->sum('total'),
            ],
            'latest_sales' => SaleResource::collection(
                Sale::query()->with(['customer', 'seller'])->latest('sale_date')->limit(5)->get()
            ),
            'low_stock_products' => ProductResource::collection(
                Product::query()
                    ->with(['categoryRelation', 'images'])
                    ->whereColumn('stock', '<=', 'minimum_stock')
                    ->orderBy('stock')
                    ->limit(5)
                    ->get()
            ),
        ]);
    }
}
