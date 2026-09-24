<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 5;

    public function index(): Response
    {
        $sales = fn ($from) => Order::query()
            ->where('status', '!=', 'cancelado')
            ->where('placed_at', '>=', $from)
            ->sum('total');

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelado')
            ->selectRaw('order_items.product_name, sum(order_items.quantity) as quantity_sold, sum(order_items.subtotal) as revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();

        $recentOrders = Order::query()
            ->with('user:id,name')
            ->orderByDesc('placed_at')
            ->limit(5)
            ->get(['id', 'order_number', 'user_id', 'total', 'status', 'placed_at']);

        $since = Carbon::now()->subDays(29)->startOfDay();
        $dailyTotals = Order::query()
            ->where('status', '!=', 'cancelado')
            ->where('placed_at', '>=', $since)
            ->selectRaw('DATE(placed_at) as day, SUM(total) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $salesTrend = collect(range(0, 29))->map(function (int $offset) use ($since, $dailyTotals) {
            $date = $since->copy()->addDays($offset);
            $key = $date->format('Y-m-d');

            return [
                'date' => $key,
                'total' => (float) ($dailyTotals[$key] ?? 0),
            ];
        })->values();

        return Inertia::render('Admin/Dashboard', [
            'alerts' => [
                'pendingPayments' => Payment::query()->where('method', 'transferencia')->where('status', 'pendiente')->count(),
                'pendingOrders' => Order::query()->where('status', 'pendiente')->count(),
                'lowStock' => Product::query()->where('active', true)->where('stock', '>', 0)->where('stock', '<=', self::LOW_STOCK_THRESHOLD)->count(),
                'outOfStock' => Product::query()->where('active', true)->where('stock', 0)->count(),
            ],
            'overview' => [
                'activeProducts' => Product::query()->where('active', true)->count(),
                'customers' => User::query()->role('cliente')->count(),
                'totalOrders' => Order::query()->count(),
                'activeSuppliers' => Supplier::query()->where('active', true)->count(),
            ],
            'summary' => [
                'today' => $sales(Carbon::today()),
                'week' => $sales(Carbon::now()->startOfWeek()),
                'month' => $sales(Carbon::now()->startOfMonth()),
                'allTime' => $sales(Carbon::createFromTimestamp(0)),
            ],
            'salesTrend' => $salesTrend,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
        ]);
    }
}
