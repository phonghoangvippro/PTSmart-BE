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
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $totalOrders = Order::count();
        $newUsers = User::where('role', 'customer')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $topProducts = Product::orderByDesc('sold_count')->limit(5)->get(['id', 'name', 'slug', 'thumbnail', 'sold_count', 'price']);

        $pendingOrders = Order::where('status', 'pending')->count();
        $todayRevenue = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        return response()->json([
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'new_users' => $newUsers,
            'top_products' => $topProducts,
        ]);
    }

    public function revenueChart(Request $request): JsonResponse
    {
        $period = $request->input('period', 'daily'); // daily, weekly, monthly
        $days = match ($period) {
            'weekly' => 7,
            'monthly' => 30,
            default => 7,
        };

        $data = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($days))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function recentOrders(): JsonResponse
    {
        $orders = Order::with(['user:id,name,email', 'items'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json(['data' => $orders]);
    }
}
