<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryMovementResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SaleResource;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\SaleDetail;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $todaySales = Sale::query()->whereDate('sale_date', today());
        $activeSales = Sale::query()->where('estado', '!=', 'anulado');
        $monthlySales = Sale::query()->whereBetween('sale_date', [now()->startOfMonth(), now()->endOfMonth()]);
        $salesTrend = Sale::query()
            ->selectRaw('DATE(sale_date) as sale_day, COUNT(*) as sales_count, COALESCE(SUM(total), 0) as total_amount')
            ->whereBetween('sale_date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('sale_day')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->sale_day,
                'sales_count' => (int) $row->sales_count,
                'total_amount' => (float) $row->total_amount,
            ]);

        $salesByPaymentMethod = Sale::query()
            ->selectRaw('payment_method, COUNT(*) as sales_count, COALESCE(SUM(total), 0) as total_amount')
            ->where('payment_status', '!=', 'anulado')
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($row) => [
                'payment_method' => $row->payment_method,
                'sales_count' => (int) $row->sales_count,
                'total_amount' => (float) $row->total_amount,
            ]);

        $salesByStatus = Sale::query()
            ->selectRaw('payment_status, COUNT(*) as sales_count, COALESCE(SUM(total), 0) as total_amount')
            ->groupBy('payment_status')
            ->orderBy('payment_status')
            ->get()
            ->map(fn ($row) => [
                'payment_status' => $row->payment_status,
                'sales_count' => (int) $row->sales_count,
                'total_amount' => (float) $row->total_amount,
            ]);

        $topProducts = SaleDetail::query()
            ->selectRaw('product_id, product_name, COALESCE(SUM(quantity), 0) as total_quantity, COALESCE(SUM(line_total), 0) as total_amount')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'product_id' => $row->product_id,
                'product_name' => $row->product_name,
                'total_quantity' => (int) $row->total_quantity,
                'total_amount' => (float) $row->total_amount,
            ]);

        $categoryBreakdown = Product::query()
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw('categories.id, categories.name, categories.legacy_key, COUNT(products.id) as products_count, COALESCE(SUM(products.stock), 0) as stock_total')
            ->groupBy('categories.id', 'categories.name', 'categories.legacy_key')
            ->orderBy('categories.name')
            ->get()
            ->map(fn ($row) => [
                'category_id' => $row->id,
                'name' => $row->name,
                'legacy_key' => $row->legacy_key,
                'products_count' => (int) $row->products_count,
                'stock_total' => (int) $row->stock_total,
            ]);

        return response()->json([
            'summary' => [
                'products_total' => Product::query()->count(),
                'products_low_stock' => Product::query()->whereColumn('stock', '<=', 'minimum_stock')->count(),
                'customers_total' => User::query()->where('user_type', 'cliente')->count(),
                'administrativos_total' => User::query()->where('user_type', 'administrativo')->count(),
                'sales_total' => Sale::query()->count(),
                'sales_today' => $todaySales->count(),
                'sales_today_amount' => (float) $todaySales->sum('total'),
                'sales_month_amount' => (float) $monthlySales->sum('total'),
                'average_ticket' => round((float) ($activeSales->avg('total') ?? 0), 2),
                'inventory_units_total' => (int) Product::query()->sum('stock'),
                'inventory_value_total' => (float) Product::query()->selectRaw('COALESCE(SUM(stock * price), 0) as total')->value('total'),
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
            'reports' => [
                'sales_trend_last_7_days' => $salesTrend,
                'sales_by_payment_method' => $salesByPaymentMethod,
                'sales_by_status' => $salesByStatus,
                'top_products' => $topProducts,
                'category_breakdown' => $categoryBreakdown,
                'recent_movements' => InventoryMovementResource::collection(
                    InventoryMovement::query()
                        ->with(['product.categoryRelation', 'product.images', 'user'])
                        ->latest()
                        ->limit(10)
                        ->get()
                ),
            ],
            'generated_at' => now()->toDateTimeString(),
        ]);
    }
}
