<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        // Current period data
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'customer')->count();

        // Previous month data (for growth calculation)
        $startOfThisMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        $thisMonthRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', $startOfThisMonth)
            ->sum('total');
        $lastMonthRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total');

        $thisMonthOrders = Order::where('created_at', '>=', $startOfThisMonth)->count();
        $lastMonthOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $thisMonthProducts = Product::where('created_at', '>=', $startOfThisMonth)->count();
        $lastMonthProducts = Product::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $thisMonthUsers = User::where('role', 'customer')
            ->where('created_at', '>=', $startOfThisMonth)->count();
        $lastMonthUsers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $topProducts = Product::orderByDesc('sold_count')
            ->limit(5)
            ->get(['id', 'name', 'slug', 'thumbnail', 'sold_count', 'price']);

        return response()->json([
            'total_revenue' => $totalRevenue,
            'revenue_growth' => $this->calcGrowth($thisMonthRevenue, $lastMonthRevenue),
            'total_orders' => $totalOrders,
            'orders_growth' => $this->calcGrowth($thisMonthOrders, $lastMonthOrders),
            'total_products' => $totalProducts,
            'products_growth' => $this->calcGrowth($thisMonthProducts, $lastMonthProducts),
            'total_users' => $totalUsers,
            'users_growth' => $this->calcGrowth($thisMonthUsers, $lastMonthUsers),
            'top_products' => $topProducts,
        ]);
    }

    public function revenueChart(Request $request): JsonResponse
    {
        $year = $request->input('year', now()->year);

        $data = Order::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill missing months with 0
        $chart = collect(range(1, 12))->map(function ($month) use ($data) {
            $found = $data->firstWhere('month', $month);
            return [
                'month' => $month,
                'revenue' => $found ? $found->revenue : 0,
                'orders' => $found ? $found->orders : 0,
            ];
        });

        return response()->json(['data' => $chart, 'year' => (int) $year]);
    }

    public function orderStatus(): JsonResponse
    {
        $statuses = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $total = $statuses->sum();

        return response()->json([
            'total' => $total,
            'statuses' => [
                'completed' => $statuses->get('completed', 0),
                'pending' => $statuses->get('pending', 0),
                'confirmed' => $statuses->get('confirmed', 0),
                'shipping' => $statuses->get('shipping', 0),
                'canceled' => $statuses->get('canceled', 0),
            ],
            'percentages' => [
                'completed' => $total > 0 ? round($statuses->get('completed', 0) / $total * 100, 1) : 0,
                'pending' => $total > 0 ? round($statuses->get('pending', 0) / $total * 100, 1) : 0,
                'confirmed' => $total > 0 ? round($statuses->get('confirmed', 0) / $total * 100, 1) : 0,
                'shipping' => $total > 0 ? round($statuses->get('shipping', 0) / $total * 100, 1) : 0,
                'canceled' => $total > 0 ? round($statuses->get('canceled', 0) / $total * 100, 1) : 0,
            ],
        ]);
    }

    public function recentOrders(): JsonResponse
    {
        $orders = Order::with(['user:id,name,email', 'items.product:id,name,thumbnail'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json(['data' => $orders]);
    }

    private function calcGrowth($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round(($current - $previous) / $previous * 100, 1);
    }
}
