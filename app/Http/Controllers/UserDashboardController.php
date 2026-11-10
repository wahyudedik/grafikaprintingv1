<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function vendorDashboard()
    {
        try {
            // Get counts for dashboard widgets
            $userCount = User::whereHas('vendorUser', function ($query) {
                $query->where('vendor_id', Auth::user()->vendorUser->first()->vendor_id);
            })->count();
            $vendorCount = Vendor::where('is_active', 1)
                ->where('id', Auth::user()->vendorUser->first()->vendor_id)
                ->count();
                
            // Get product count if Produk model exists
            $productCount = 0;
            if (class_exists('App\Models\Vendor\Produk')) {
                $productCount = Produk::count();
            } else {
                $productCount = 256; // Placeholder
            }

            // Get bahan (materials) count
            $bahanCount = Bahan::count();

            // Get today's transactions
            $todayTransactions = Transaksi::whereDate('tanggal_dibuat', Carbon::today())->count();
            $yesterdayTransactions = Transaksi::whereDate('tanggal_dibuat', Carbon::yesterday())->count();

            // Monthly transactions
            $monthlyTransactions = Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->year)
                ->count();

            $lastMonthTransactions = Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->subMonth()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->subMonth()->year)
                ->count();

            // Monthly revenue
            $monthlyRevenue = Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->year)
                ->sum('total_harga') / 1000000; // Convert to millions

            $lastMonthRevenue = Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->subMonth()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->subMonth()->year)
                ->sum('total_harga') / 1000000;

            // Average order value
            $averageOrderValue = 0;
            $lastMonthAverageOrderValue = 0;

            if ($monthlyTransactions > 0) {
                $averageOrderValue = (Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->month)
                    ->whereYear('tanggal_dibuat', Carbon::now()->year)
                    ->sum('total_harga') / $monthlyTransactions) / 1000; // Convert to thousands
            }

            if ($lastMonthTransactions > 0) {
                $lastMonthAverageOrderValue = (Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->subMonth()->month)
                    ->whereYear('tanggal_dibuat', Carbon::now()->subMonth()->year)
                    ->sum('total_harga') / $lastMonthTransactions) / 1000;
            }

            // Calculate growth percentages
            $todayGrowth = $yesterdayTransactions > 0
                ? round((($todayTransactions - $yesterdayTransactions) / $yesterdayTransactions) * 100)
                : 0;

            $monthlyGrowth = $lastMonthTransactions > 0
                ? round((($monthlyTransactions - $lastMonthTransactions) / $lastMonthTransactions) * 100)
                : 0;

            $monthlyRevenueGrowth = $lastMonthRevenue > 0
                ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
                : 0;

            $averageOrderValueGrowth = $lastMonthAverageOrderValue > 0
                ? round((($averageOrderValue - $lastMonthAverageOrderValue) / $lastMonthAverageOrderValue) * 100)
                : 0;

            // Get popular products data
            $popularProducts = ['labels' => [], 'data' => []];

            if (class_exists('App\Models\Vendor\Produk')) {
                // Get the most frequently ordered products
                $topProducts = TransaksiItem::select('produk_id', DB::raw('COUNT(*) as order_count'))
                    ->groupBy('produk_id')
                    ->orderBy('order_count', 'desc')
                    ->limit(6)
                    ->get();

                foreach ($topProducts as $item) {
                    $produk = Produk::find($item->produk_id);
                    if ($produk) {
                        $popularProducts['labels'][] = $produk->nama_produk ?? 'Product #' . $item->produk_id;
                        $popularProducts['data'][] = $item->order_count;
                    }
                }
            }

            // If no product data, use placeholder
            if (empty($popularProducts['labels'])) {
                $popularProducts = [
                    'labels' => ['Banner Printing', 'Business Cards', 'Flyers', 'Stickers', 'Posters', 'Brochures'],
                    'data' => [21, 17, 15, 12, 10, 8]
                ];
            }

            // Get monthly revenue data for the last 6 months
            $revenueData = ['labels' => [], 'data' => []];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $revenueData['labels'][] = $month->format('F');

                $revenue = Transaksi::whereMonth('tanggal_dibuat', $month->month)
                    ->whereYear('tanggal_dibuat', $month->year)
                    ->sum('total_harga') / 1000000; // Convert to millions

                $revenueData['data'][] = round($revenue, 1);
            }

            // Get low stock materials
            $lowStockMaterials = Bahan::where('stok', '<', 10)
                ->where('stok', '>', 0)
                ->orderBy('stok', 'asc')
                ->limit(5)
                ->get();

            // Get out of stock materials
            $outOfStockCount = Bahan::where('stok', '=', 0)->count();

            // Get pending orders count
            $pendingOrdersCount = Transaksi::where('status', 'pending')->count();

            // Get processing orders count
            $processingOrdersCount = Transaksi::where('status', 'processing')->count();

            // Get completed orders count for current month
            $completedOrdersCount = Transaksi::where('status', 'completed')
                ->whereMonth('tanggal_dibuat', Carbon::now()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->year)
                ->count();

            return view('dashboard', compact(
                'userCount',
                'vendorCount',
                'productCount',
                'bahanCount',
                'todayTransactions',
                'todayGrowth',
                'monthlyTransactions',
                'monthlyGrowth',
                'monthlyRevenue',
                'monthlyRevenueGrowth',
                'averageOrderValue',
                'averageOrderValueGrowth',
                'popularProducts',
                'revenueData',
                'lowStockMaterials',
                'outOfStockCount',
                'pendingOrdersCount',
                'processingOrdersCount',
                'completedOrdersCount'
            ))->with('toast_success', 'Welcome to your dashboard');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    public function devDashboard()
    {
        try {
            $vendor = $this->vendorDashboard();
            return view('dev.dashboard', compact('vendor'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }
}
