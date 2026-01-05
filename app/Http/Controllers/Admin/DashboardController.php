<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_revenue' => Order::whereIn('status', ['processing', 'completed'])
                ->sum('total_amount'),

            'total_orders' => Order::count(),

            // Pending = belum diproses (tidak perlu payment_status)
            'pending_orders' => Order::where('status', 'pending')->count(),

            'total_products' => Product::count(),

            'total_customers' => User::where('role', 'customer')->count(),

            'low_stock' => Product::where('stock', '<=', 5)->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Produk Terlaris
        $topProducts = Product::withCount(['orderItems as sold' => function ($q) {
            $q->select(DB::raw('SUM(quantity)'))
                ->whereHas('order', function ($query) {
                    $query->whereIn('status', ['processing', 'completed']);
                });
        }])
            ->having('sold', '>', 0)
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        // Grafik Pendapatan 7 Hari Terakhir
        $revenueChart = Order::select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total'),
        ])
            ->whereIn('status', ['processing', 'completed'])
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'topProducts',
            'revenueChart'
        ));
    }
}