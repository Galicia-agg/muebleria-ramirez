<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    private const PERIODS = ['today', 'yesterday', 'week', 'month', 'year', 'custom'];

    public function index(Request $request): Response
    {
        $period = in_array($request->input('period'), self::PERIODS, true) ? $request->input('period') : 'month';

        [$from, $to] = $this->resolveRange($period, $request->input('from'), $request->input('to'));

        $rangeDays = $from->diffInDays($to) + 1;
        $prevFrom = $from->copy()->subDays($rangeDays)->startOfDay();
        $prevTo = $from->copy()->subDay()->endOfDay();

        $salesInRange = fn (Carbon $rangeFrom, Carbon $rangeTo) => Order::query()
            ->where('status', '!=', 'cancelado')
            ->whereBetween('placed_at', [$rangeFrom, $rangeTo]);

        $current = $salesInRange($from, $to)->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as count')->first();
        $previous = $salesInRange($prevFrom, $prevTo)->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as count')->first();

        $summary = [
            'total' => (float) $current->total,
            'count' => (int) $current->count,
            'average' => $current->count > 0 ? (float) $current->total / $current->count : 0,
            'previousTotal' => (float) $previous->total,
            'previousCount' => (int) $previous->count,
        ];

        // Bucket by month for long ranges so a full year isn't 365 noisy daily points.
        $bucket = $rangeDays > 60 ? 'month' : 'day';

        if ($bucket === 'day') {
            $totals = $salesInRange($from, $to)
                ->selectRaw('DATE(placed_at) as key, SUM(total) as total')
                ->groupBy('key')
                ->pluck('total', 'key');

            $salesTrend = collect();
            $cursor = $from->copy()->startOfDay();
            $lastDay = $to->copy()->startOfDay();
            while ($cursor->lte($lastDay)) {
                $key = $cursor->format('Y-m-d');
                $salesTrend->push(['date' => $key, 'total' => (float) ($totals[$key] ?? 0)]);
                $cursor->addDay();
            }
        } else {
            $totals = $salesInRange($from, $to)
                ->selectRaw("TO_CHAR(placed_at, 'YYYY-MM') as key, SUM(total) as total")
                ->groupBy('key')
                ->pluck('total', 'key');

            $salesTrend = collect();
            $cursor = $from->copy()->startOfMonth();
            $lastMonth = $to->copy()->startOfMonth();
            while ($cursor->lte($lastMonth)) {
                $key = $cursor->format('Y-m');
                $salesTrend->push(['date' => $key.'-01', 'total' => (float) ($totals[$key] ?? 0)]);
                $cursor->addMonth();
            }
        }

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelado')
            ->whereBetween('orders.placed_at', [$from, $to])
            ->selectRaw('order_items.product_name, sum(order_items.quantity) as quantity_sold, sum(order_items.subtotal) as revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('quantity_sold')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Reports/Index', [
            'filters' => [
                'period' => $period,
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
            ],
            'summary' => $summary,
            'salesTrend' => $salesTrend,
            'trendBucket' => $bucket,
            'topProducts' => $topProducts,
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveRange(string $period, ?string $from, ?string $to): array
    {
        $now = Carbon::now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'custom' => [
                $from ? Carbon::parse($from)->startOfDay() : $now->copy()->startOfMonth(),
                $to ? Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay(),
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
}
