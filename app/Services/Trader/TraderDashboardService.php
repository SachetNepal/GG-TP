<?php

namespace App\Services\Trader;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TraderDashboardService
{
    public function summary(User $user): array
    {
        $trader = $user->trader;
        abort_if(!$trader, 403, 'Trader profile not found');

        $shopIds = $trader->shops()->pluck('shop_id');
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $ordersQuery = Order::query()
            ->whereHas('items.product', fn ($q) => $q->whereIn('shop_id', $shopIds));

        $todayOrders = (clone $ordersQuery)->whereDate('order_date', $today)->count();
        $pendingOrders = (clone $ordersQuery)->whereIn('status', ['pending', 'placed'])->count();
        $weeklyEarnings = (clone $ordersQuery)
            ->whereBetween('order_date', [$weekStart, $weekEnd])
            ->sum('amount');

        $lowStockAlerts = Product::query()
            ->whereIn('shop_id', $shopIds)
            ->where('product_in_stock', '<=', 5)
            ->count();

        $ordersBySlot = DB::connection('oracle')
            ->table('COLLECTION_SLOT as cs')
            ->join('ORDER as o', 'cs.order_id', '=', 'o.order_id')
            ->join('ORDER_ITEM as oi', 'o.order_id', '=', 'oi.order_id')
            ->join('PRODUCT as p', 'oi.product_id', '=', 'p.product_id')
            ->whereIn('p.shop_id', $shopIds)
            ->select('cs.date', 'cs.time', DB::raw('COUNT(DISTINCT o.order_id) as order_count'))
            ->groupBy('cs.date', 'cs.time')
            ->orderBy('cs.date')
            ->orderBy('cs.time')
            ->get();

        $weeklyTrend = (clone $ordersQuery)
            ->whereBetween('order_date', [$weekStart, $weekEnd])
            ->selectRaw('TRUNC(order_date) as day, SUM(amount) as total')
            ->groupByRaw('TRUNC(order_date)')
            ->orderByRaw('TRUNC(order_date)')
            ->get();

        return [
            'stats' => [
                'total_orders_today' => $todayOrders,
                'pending_orders' => $pendingOrders,
                'low_stock_alerts' => $lowStockAlerts,
                'weekly_earnings' => (float) $weeklyEarnings,
            ],
            'orders_by_collection_slot' => $ordersBySlot,
            'weekly_sales_trend' => $weeklyTrend,
        ];
    }
}

