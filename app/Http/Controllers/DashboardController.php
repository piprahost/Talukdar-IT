<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Service;
use App\Models\WarrantySubmission;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $this->authorizePermission('view dashboard');
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        // Sales Statistics
        $todaySales = Sale::whereDate('sale_date', $today)
            ->where('status', 'completed')
            ->sum('total_amount') ?? 0;
        
        $monthSales = Sale::where('sale_date', '>=', $thisMonth)
            ->where('status', 'completed')
            ->sum('total_amount') ?? 0;
        
        $lastMonthSales = Sale::whereBetween('sale_date', [$lastMonth, $lastMonthEnd])
            ->where('status', 'completed')
            ->sum('total_amount') ?? 0;
        
        $salesGrowth = $lastMonthSales > 0 
            ? (($monthSales - $lastMonthSales) / $lastMonthSales) * 100 
            : 0;
        
        // Purchase Statistics
        $monthPurchases = Purchase::where('order_date', '>=', $thisMonth)
            ->sum('total_amount') ?? 0;
        
        $lastMonthPurchases = Purchase::whereBetween('order_date', [$lastMonth, $lastMonthEnd])
            ->sum('total_amount') ?? 0;
        
        $purchaseGrowth = $lastMonthPurchases > 0 
            ? (($monthPurchases - $lastMonthPurchases) / $lastMonthPurchases) * 100 
            : 0;
        
        // Product Statistics
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'reorder_level')
            ->where('is_active', true)
            ->count();
        
        // Customer & Supplier Statistics
        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();
        
        // Due Amounts
        $customerDues = Sale::where('status', 'completed')
            ->sum('due_amount') ?? 0;
        
        $supplierDues = Purchase::sum('due_amount') ?? 0;
        
        // Recent Sales (Last 7 days)
        $recentSales = Sale::with(['customer'])
            ->where('status', 'completed')
            ->latest()
            ->take(10)
            ->get();
        
        // Sales Chart Data (Last 30 days) – fill every day so chart always has data
        $salesByDate = Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('sale_date', '>=', Carbon::now()->subDays(29))
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy(function ($item) {
                return $item->date;
            });

        $salesChartData = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $salesChartData->push([
                'date' => $date->format('M d'),
                'total' => (float) ($salesByDate->get($dateStr)->total ?? 0),
            ]);
        }
        
        // Top Selling Products (Last 30 days)
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->where('sales.sale_date', '>=', Carbon::now()->subDays(30))
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
        
        // Payment Status Summary
        $salesPaymentStatus = Sale::where('status', 'completed')
            ->select(
                'payment_status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');
        
        // Pending Services
        $pendingServices = Service::whereIn('status', ['pending', 'in_progress'])->count();
        
        // Pending Warranty Submissions
        $pendingWarranties = WarrantySubmission::whereIn('status', ['pending', 'received', 'in_progress'])->count();
        
        // Profit Calculation (Sales - Purchases for this month)
        $monthProfit = $monthSales - $monthPurchases;
        $profitMargin = $monthSales > 0 ? ($monthProfit / $monthSales) * 100 : 0;
        
        return view('dashboard.index', compact(
            'todaySales',
            'monthSales',
            'salesGrowth',
            'monthPurchases',
            'purchaseGrowth',
            'totalProducts',
            'activeProducts',
            'lowStockProducts',
            'totalCustomers',
            'totalSuppliers',
            'customerDues',
            'supplierDues',
            'recentSales',
            'salesChartData',
            'topProducts',
            'salesPaymentStatus',
            'pendingServices',
            'pendingWarranties',
            'monthProfit',
            'profitMargin'
        ));
    }
}
