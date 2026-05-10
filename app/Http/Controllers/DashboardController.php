<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        try {
            $months = [
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'Mei',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Agu',
                9 => 'Sep',
                10 => 'Okt',
                11 => 'Nov',
                12 => 'Des',
            ];

            /*
            |--------------------------------------------------------------------------
            | TOTAL REVENUE
            |--------------------------------------------------------------------------
            */
            // Hari ini
            $todayRevenue = Order::where('status', 'paid')
                ->whereDate('created_at', today())
                ->sum('total_amount');
            // Bulan ini
            $monthRevenue = Order::where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');
            // Tahun ini
            $yearRevenue = Order::where('status', 'paid')
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');
            // Keseluruhan
            $totalRevenue = Order::where('status', 'paid')
                ->sum('total_amount');

            $yesterdayRevenue = \App\Models\Order::where('status', 'paid')
                ->whereDate('created_at', today()->subDay())
                ->sum('total_amount');

            $growthRevenue = 0;

            if ($yesterdayRevenue > 0) {
                $growthRevenue =
                    (($todayRevenue - $yesterdayRevenue)
                        / $yesterdayRevenue) * 100;
                $growthRevenue = round($growthRevenue, 1);
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL ORDER
            |--------------------------------------------------------------------------
            */
            // Hari ini
            $todayOrders = Order::whereDate('created_at', today())
                ->count();
            // Bulan ini
            $monthOrders = Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            // Tahun ini
            $yearOrders = Order::whereYear('created_at', now()->year)
                ->count();
            // Keseluruhan
            $totalOrders = Order::count();

            /*
            |--------------------------------------------------------------------------
            | MENU
            |--------------------------------------------------------------------------
            */
            $totalMenus = Menu::count();
            $readyMenus = Menu::where('is_ready', true)->count();
            $emptyMenus = Menu::where('is_ready', false)->count();

            /*
            |--------------------------------------------------------------------------
            | BEST SELLER MENU
            |--------------------------------------------------------------------------
            */
            $bestSellerMenus = OrderItem::with([
                'menu.fotoMenus'
            ])
                ->select(
                    'menu_id',
                    'nama_menu',
                    DB::raw('SUM(qty) as total_qty'),
                    DB::raw('SUM(harga * qty) as total_income')
                )
                ->groupBy('menu_id', 'nama_menu')
                ->orderByDesc('total_qty')
                ->take(5)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | REVENUE CHART
            |--------------------------------------------------------------------------
            */
            $revenueChart = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
                ->where('status', 'paid')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month', 'ASC')
                ->get();

            $chartData = [];

            for ($i = 1; $i <= 12; $i++) {

                $found = $revenueChart->firstWhere('month', $i);

                $chartData[] = [
                    'month' => $months[$i],
                    'total' => $found ? (float) $found->total : 0,
                ];
            }

            $latestOrders = Order::with([
                'items',
                'meja'
            ])
                ->latest()
                ->take(10)
                ->get();


            $data = [
                'pageTitle' => 'Dashboard',
                'revenue' => [
                    'today' => $todayRevenue,
                    'growthRevenue' => $growthRevenue,
                    'month' => $monthRevenue,
                    'year' => $yearRevenue,
                    'total' => $totalRevenue,
                ],
                'orders' => [
                    'today' => $todayOrders,
                    'month' => $monthOrders,
                    'year' => $yearOrders,
                    'total' => $totalOrders,
                ],
                'menus' => [
                    'total' => $totalMenus,
                    'ready' => $readyMenus,
                    'empty' => $emptyMenus,
                ],
                'bestSellerMenus' => $bestSellerMenus,
                'latestOrders' => $latestOrders,
                'revenue_chart' => $chartData,
            ];

            return view('dashboard.index', $data);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }
}
