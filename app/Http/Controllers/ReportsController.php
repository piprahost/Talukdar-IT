<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportsController extends Controller
{
    // ==================== SALES REPORTS ====================
    
    public function salesReport(Request $request)
    {
        $this->authorizePermission('view sales-reports');
        $period = $request->get('period', 'month'); // daily, weekly, monthly, yearly, custom
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        // Adjust dates based on period
        if ($period === 'today') {
            $dateFrom = Carbon::today()->format('Y-m-d');
            $dateTo = Carbon::today()->format('Y-m-d');
        } elseif ($period === 'week') {
            $dateFrom = Carbon::now()->startOfWeek()->format('Y-m-d');
            $dateTo = Carbon::now()->endOfWeek()->format('Y-m-d');
        } elseif ($period === 'month') {
            $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
            $dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($period === 'year') {
            $dateFrom = Carbon::now()->startOfYear()->format('Y-m-d');
            $dateTo = Carbon::now()->endOfYear()->format('Y-m-d');
        }
        
        $query = Sale::where('status', 'completed')
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);
        
        $sales = $query->get();
        
        $stats = [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('total_amount'),
            'total_paid' => $sales->sum('paid_amount'),
            'total_due' => $sales->sum('due_amount'),
            'total_discount' => $sales->sum('discount_amount'),
            'total_tax' => $sales->sum('tax_amount'),
            'average_sale' => $sales->count() > 0 ? $sales->avg('total_amount') : 0,
        ];
        
        // Daily breakdown for trends
        $dailyBreakdown = $sales->groupBy(function($sale) {
            return $sale->sale_date->format('Y-m-d');
        })->map(function($group) {
            return [
                'date' => $group->first()->sale_date->format('d M Y'),
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ];
        })->values();
        
        return view('reports.sales.index', compact('period', 'dateFrom', 'dateTo', 'stats', 'dailyBreakdown', 'sales'));
    }
    
    public function salesByProduct(Request $request)
    {
        $this->authorizePermission('view sales-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $salesByProduct = SaleItem::whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'completed')
                  ->whereBetween('sale_date', [$dateFrom, $dateTo]);
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_amount'))
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_quantity')
            ->get();
        
        return view('reports.sales.by-product', compact('dateFrom', 'dateTo', 'salesByProduct'));
    }
    
    public function salesByCategory(Request $request)
    {
        $this->authorizePermission('view sales-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $salesByCategory = SaleItem::whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'completed')
                  ->whereBetween('sale_date', [$dateFrom, $dateTo]);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.id', 'categories.name', 
                     DB::raw('SUM(sale_items.quantity) as total_quantity'),
                     DB::raw('SUM(sale_items.subtotal) as total_amount'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_amount')
            ->get();
        
        return view('reports.sales.by-category', compact('dateFrom', 'dateTo', 'salesByCategory'));
    }
    
    public function salesByBrand(Request $request)
    {
        $this->authorizePermission('view sales-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $salesByBrand = SaleItem::whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'completed')
                  ->whereBetween('sale_date', [$dateFrom, $dateTo]);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->select('brands.id', 'brands.name',
                     DB::raw('SUM(sale_items.quantity) as total_quantity'),
                     DB::raw('SUM(sale_items.subtotal) as total_amount'))
            ->groupBy('brands.id', 'brands.name')
            ->orderByDesc('total_amount')
            ->get();
        
        return view('reports.sales.by-brand', compact('dateFrom', 'dateTo', 'salesByBrand'));
    }
    
    public function salesByCustomer(Request $request)
    {
        $this->authorizePermission('view sales-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $salesByCustomer = Sale::where('status', 'completed')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->select('customer_id', 
                     DB::raw('COUNT(*) as total_orders'),
                     DB::raw('SUM(total_amount) as total_amount'),
                     DB::raw('SUM(paid_amount) as total_paid'),
                     DB::raw('SUM(due_amount) as total_due'))
            ->groupBy('customer_id')
            ->with('customer')
            ->orderByDesc('total_amount')
            ->get();
        
        return view('reports.sales.by-customer', compact('dateFrom', 'dateTo', 'salesByCustomer'));
    }
    
    public function topSellingProducts(Request $request)
    {
        $this->authorizePermission('view sales-reports');
        $limit = $request->get('limit', 10);
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $topProducts = SaleItem::whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'completed')
                  ->whereBetween('sale_date', [$dateFrom, $dateTo]);
            })
            ->select('product_id',
                     DB::raw('SUM(quantity) as total_quantity'),
                     DB::raw('SUM(subtotal) as total_amount'),
                     DB::raw('AVG(unit_price) as avg_price'))
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
        
        return view('reports.sales.top-products', compact('dateFrom', 'dateTo', 'topProducts', 'limit'));
    }
    
    // ==================== PURCHASE REPORTS ====================
    
    public function purchaseReport(Request $request)
    {
        $this->authorizePermission('view purchase-reports');
        $period = $request->get('period', 'month');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        if ($period === 'today') {
            $dateFrom = Carbon::today()->format('Y-m-d');
            $dateTo = Carbon::today()->format('Y-m-d');
        } elseif ($period === 'week') {
            $dateFrom = Carbon::now()->startOfWeek()->format('Y-m-d');
            $dateTo = Carbon::now()->endOfWeek()->format('Y-m-d');
        } elseif ($period === 'month') {
            $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
            $dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($period === 'year') {
            $dateFrom = Carbon::now()->startOfYear()->format('Y-m-d');
            $dateTo = Carbon::now()->endOfYear()->format('Y-m-d');
        }
        
        $purchases = Purchase::where('status', 'received')
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->get();
        
        $stats = [
            'total_purchases' => $purchases->count(),
            'total_amount' => $purchases->sum('total_amount'),
            'total_paid' => $purchases->sum('paid_amount'),
            'total_due' => $purchases->sum('due_amount'),
            'average_purchase' => $purchases->count() > 0 ? $purchases->avg('total_amount') : 0,
        ];

        $export = $request->get('export');
        if ($export === 'csv') {
            $csv = "PO Number,Date,Supplier,Total,Paid,Due\n";
            foreach ($purchases as $p) {
                $csv .= '"' . $p->po_number . '","' . $p->order_date->format('Y-m-d') . '","' . str_replace('"', '""', $p->supplier->name ?? '') . '",৳' . number_format($p->total_amount, 2) . ',৳' . number_format($p->paid_amount, 2) . ',৳' . number_format($p->due_amount, 2) . "\n";
            }
            return Response::make($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="purchases-' . $dateFrom . '-to-' . $dateTo . '.csv"']);
        }
        if (in_array($export, ['pdf', 'xlsx'])) {
            return redirect()->route('reports.purchases.index', $request->only(['period', 'date_from', 'date_to']))->with('info', 'PDF/Excel export will be available in a future update.');
        }

        return view('reports.purchases.index', compact('period', 'dateFrom', 'dateTo', 'stats', 'purchases'));
    }
    
    public function purchasesBySupplier(Request $request)
    {
        $this->authorizePermission('view purchase-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $purchasesBySupplier = Purchase::where('status', 'received')
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->select('supplier_id',
                     DB::raw('COUNT(*) as total_orders'),
                     DB::raw('SUM(total_amount) as total_amount'),
                     DB::raw('SUM(paid_amount) as total_paid'),
                     DB::raw('SUM(due_amount) as total_due'))
            ->groupBy('supplier_id')
            ->with('supplier')
            ->orderByDesc('total_amount')
            ->get();
        
        return view('reports.purchases.by-supplier', compact('dateFrom', 'dateTo', 'purchasesBySupplier'));
    }
    
    public function costAnalysis(Request $request)
    {
        $this->authorizePermission('view purchase-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $costAnalysis = PurchaseItem::whereHas('purchaseOrder', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'received')
                  ->whereBetween('order_date', [$dateFrom, $dateTo]);
            })
            ->select('product_id',
                     DB::raw('SUM(quantity) as total_quantity'),
                     DB::raw('AVG(cost_price) as avg_cost_price'),
                     DB::raw('SUM(cost_price * quantity) as total_cost'))
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_cost')
            ->get();
        
        return view('reports.purchases.cost-analysis', compact('dateFrom', 'dateTo', 'costAnalysis'));
    }
    
    // ==================== FINANCIAL REPORTS ====================
    
    public function profitLossReport(Request $request)
    {
        $this->authorizePermission('view financial-reports');
        // This should use the accounting Profit & Loss, but also include gross margin details
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        // Get sales data
        $sales = Sale::where('status', 'completed')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->get();
        
        $totalRevenue = $sales->sum('total_amount');
        
        // Calculate COGS (Cost of Goods Sold)
        $cogs = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $cogs += $item->product->cost_price * $item->quantity;
                }
            }
        }
        
        $grossProfit = $totalRevenue - $cogs;
        $grossProfitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        
        // Get expenses
        $expenses = Expense::whereIn('status', ['approved', 'paid'])
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $grossProfit - $totalExpenses;
        $netProfitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        $export = $request->get('export');
        if ($export === 'csv') {
            $csv = "Profit & Loss Report\nFrom,$dateFrom\nTo,$dateTo\n\n";
            $csv .= "Total Revenue,৳" . number_format($totalRevenue, 2) . "\n";
            $csv .= "Cost of Goods Sold,৳" . number_format($cogs, 2) . "\n";
            $csv .= "Gross Profit,৳" . number_format($grossProfit, 2) . "\n";
            $csv .= "Gross Profit Margin %," . number_format($grossProfitMargin, 2) . "\n";
            $csv .= "Total Expenses,৳" . number_format($totalExpenses, 2) . "\n";
            $csv .= "Net Profit/Loss,৳" . number_format($netProfit, 2) . "\n";
            $csv .= "Net Profit Margin %," . number_format($netProfitMargin, 2) . "\n";
            return Response::make($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="profit-loss-' . $dateFrom . '-to-' . $dateTo . '.csv"']);
        }
        if (in_array($export, ['pdf', 'xlsx'])) {
            return redirect()->route('reports.financial.profit-loss', $request->only(['date_from', 'date_to']))->with('info', 'PDF/Excel export will be available in a future update.');
        }
        
        return view('reports.financial.profit-loss', compact('dateFrom', 'dateTo', 'totalRevenue', 'cogs', 'grossProfit', 'grossProfitMargin', 'totalExpenses', 'netProfit', 'netProfitMargin', 'expenses'));
    }
    
    public function grossMarginByProduct(Request $request)
    {
        $this->authorizePermission('view financial-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $marginAnalysis = SaleItem::whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'completed')
                  ->whereBetween('sale_date', [$dateFrom, $dateTo]);
            })
            ->select('product_id',
                     DB::raw('SUM(quantity) as total_quantity'),
                     DB::raw('SUM(subtotal) as total_revenue'),
                     DB::raw('AVG(unit_price) as avg_selling_price'))
            ->groupBy('product_id')
            ->with('product')
            ->get()
            ->map(function($item) {
                if ($item->product) {
                    $totalCost = $item->product->cost_price * $item->total_quantity;
                    $grossProfit = $item->total_revenue - $totalCost;
                    $margin = $item->total_revenue > 0 ? ($grossProfit / $item->total_revenue) * 100 : 0;
                    
                    return [
                        'product' => $item->product,
                        'quantity' => $item->total_quantity,
                        'revenue' => $item->total_revenue,
                        'cost' => $totalCost,
                        'gross_profit' => $grossProfit,
                        'margin' => $margin,
                        'avg_selling_price' => $item->avg_selling_price,
                    ];
                }
                return null;
            })
            ->filter()
            ->sortByDesc('gross_profit')
            ->values();

        $export = $request->get('export');
        if ($export === 'csv') {
            $csv = "Product,Gross Margin %\n";
            foreach ($marginAnalysis as $item) {
                $csv .= '"' . str_replace('"', '""', $item['product']->name) . '",' . number_format($item['margin'], 2) . "\n";
            }
            return Response::make($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="gross-margin-product-' . $dateFrom . '-to-' . $dateTo . '.csv"']);
        }
        if (in_array($export, ['pdf', 'xlsx'])) {
            return redirect()->route('reports.financial.gross-margin-product', $request->only(['date_from', 'date_to']))->with('info', 'PDF/Excel export will be available in a future update.');
        }
        
        return view('reports.financial.gross-margin', compact('dateFrom', 'dateTo', 'marginAnalysis'));
    }
    
    public function grossMarginByCategory(Request $request)
    {
        $this->authorizePermission('view financial-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $marginByCategory = SaleItem::whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'completed')
                  ->whereBetween('sale_date', [$dateFrom, $dateTo]);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.id', 'categories.name',
                     DB::raw('SUM(sale_items.quantity) as total_quantity'),
                     DB::raw('SUM(sale_items.subtotal) as total_revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function($item) use ($dateFrom, $dateTo) {
                // Calculate total cost for this category
                $sales = SaleItem::whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                        $q->where('status', 'completed')
                          ->whereBetween('sale_date', [$dateFrom, $dateTo]);
                    })
                    ->join('products', 'sale_items.product_id', '=', 'products.id')
                    ->where('products.category_id', $item->id)
                    ->get();
                
                $totalCost = 0;
                foreach ($sales as $saleItem) {
                    if ($saleItem->product) {
                        $totalCost += $saleItem->product->cost_price * $saleItem->quantity;
                    }
                }
                
                $grossProfit = $item->total_revenue - $totalCost;
                $margin = $item->total_revenue > 0 ? ($grossProfit / $item->total_revenue) * 100 : 0;
                
                return [
                    'category' => (object)['id' => $item->id, 'name' => $item->name],
                    'quantity' => $item->total_quantity,
                    'revenue' => $item->total_revenue,
                    'cost' => $totalCost,
                    'gross_profit' => $grossProfit,
                    'margin' => $margin,
                ];
            })
            ->sortByDesc('gross_profit')
            ->values();
        
        return view('reports.financial.gross-margin-category', compact('dateFrom', 'dateTo', 'marginByCategory'));
    }
    
    public function cashFlowReport(Request $request)
    {
        $this->authorizePermission('view financial-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        // Cash Inflows
        $customerPayments = Payment::where('payment_type', 'customer')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount');
        
        $directSales = Sale::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->whereRaw('paid_amount = total_amount') // Fully paid at time of sale
            ->sum('paid_amount');
        
        $totalCashIn = $customerPayments + $directSales;
        
        // Cash Outflows
        $supplierPayments = Payment::where('payment_type', 'supplier')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount');
        
        $expenses = Expense::where('status', 'paid')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount');
        
        $totalCashOut = $supplierPayments + $expenses;
        
        $netCashFlow = $totalCashIn - $totalCashOut;
        
        // Daily breakdown
        $dailyFlow = collect();
        $currentDate = Carbon::parse($dateFrom);
        while ($currentDate->lte(Carbon::parse($dateTo))) {
            $dayIn = Payment::where('payment_type', 'customer')
                    ->whereDate('payment_date', $currentDate->format('Y-m-d'))
                    ->sum('amount');
            $dayOut = Payment::where('payment_type', 'supplier')
                    ->whereDate('payment_date', $currentDate->format('Y-m-d'))
                    ->sum('amount');
            $dayExpenses = Expense::where('status', 'paid')
                    ->whereDate('payment_date', $currentDate->format('Y-m-d'))
                    ->sum('amount');
            
            $dailyFlow->push([
                'date' => $currentDate->format('Y-m-d'),
                'date_label' => $currentDate->format('d M Y'),
                'cash_in' => $dayIn,
                'cash_out' => $dayOut + $dayExpenses,
                'net' => $dayIn - ($dayOut + $dayExpenses),
            ]);
            
            $currentDate->addDay();
        }
        
        $export = $request->get('export');
        if ($export === 'csv') {
            $csv = "Cash Flow Report\nFrom,$dateFrom\nTo,$dateTo\n\nTotal Cash In,৳" . number_format($totalCashIn, 2) . "\nTotal Cash Out,৳" . number_format($totalCashOut, 2) . "\nNet Cash Flow,৳" . number_format($netCashFlow, 2) . "\n";
            return Response::make($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="cash-flow-' . $dateFrom . '-to-' . $dateTo . '.csv"']);
        }
        if (in_array($export, ['pdf', 'xlsx'])) {
            return redirect()->route('reports.financial.cash-flow', $request->only(['date_from', 'date_to']))->with('info', 'PDF/Excel export will be available in a future update.');
        }

        return view('reports.financial.cash-flow', compact('dateFrom', 'dateTo', 'totalCashIn', 'totalCashOut', 'netCashFlow', 'dailyFlow', 'customerPayments', 'directSales', 'supplierPayments', 'expenses'));
    }
    
    public function accountsReceivable(Request $request)
    {
        $this->authorizePermission('view financial-reports');
        $customers = Customer::withSum(['sales' => function($q) {
                $q->where('status', 'completed');
            }], 'due_amount')
            ->having('sales_sum_due_amount', '>', 0)
            ->get();
        
        $totalReceivable = $customers->sum('sales_sum_due_amount');
        
        // Get aging analysis
        $agingAnalysis = [
            'current' => 0,
            '1_30' => 0,
            '31_60' => 0,
            '61_90' => 0,
            'over_90' => 0,
        ];
        
        foreach ($customers as $customer) {
            $sales = Sale::where('customer_id', $customer->id)
                ->where('status', 'completed')
                ->where('due_amount', '>', 0)
                ->get();
            
            foreach ($sales as $sale) {
                $daysPastDue = Carbon::now()->diffInDays($sale->due_date ?? $sale->sale_date, false);
                
                if ($daysPastDue <= 0) {
                    $agingAnalysis['current'] += $sale->due_amount;
                } elseif ($daysPastDue <= 30) {
                    $agingAnalysis['1_30'] += $sale->due_amount;
                } elseif ($daysPastDue <= 60) {
                    $agingAnalysis['31_60'] += $sale->due_amount;
                } elseif ($daysPastDue <= 90) {
                    $agingAnalysis['61_90'] += $sale->due_amount;
                } else {
                    $agingAnalysis['over_90'] += $sale->due_amount;
                }
            }
        }

        $export = $request->get('export');
        if ($export === 'csv') {
            $csv = "Customer,Total Due\n";
            foreach ($customers as $c) {
                $csv .= '"' . str_replace('"', '""', $c->name) . '",৳' . number_format($c->sales_sum_due_amount ?? 0, 2) . "\n";
            }
            $csv .= "\nTotal Receivable,৳" . number_format($totalReceivable, 2) . "\n";
            return Response::make($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="accounts-receivable.csv"']);
        }
        if (in_array($export, ['pdf', 'xlsx'])) {
            return redirect()->route('reports.financial.accounts-receivable')->with('info', 'PDF/Excel export will be available in a future update.');
        }
        
        return view('reports.financial.accounts-receivable', compact('customers', 'totalReceivable', 'agingAnalysis'));
    }
    
    public function accountsPayable(Request $request)
    {
        $this->authorizePermission('view financial-reports');
        $suppliers = Supplier::withSum(['purchases' => function($q) {
                $q->where('status', 'received');
            }], 'due_amount')
            ->having('purchases_sum_due_amount', '>', 0)
            ->get();
        
        $totalPayable = $suppliers->sum('purchases_sum_due_amount');
        
        return view('reports.financial.accounts-payable', compact('suppliers', 'totalPayable'));
    }
    
    // ==================== INVENTORY REPORTS ====================
    
    public function stockValuation(Request $request)
    {
        $this->authorizePermission('view inventory-reports');
        $products = Product::with(['category', 'brand'])
            ->where('status', 'active')
            ->get()
            ->map(function($product) {
                return [
                    'product' => $product,
                    'stock_quantity' => $product->stock_quantity,
                    'cost_price' => $product->cost_price,
                    'selling_price' => $product->selling_price,
                    'total_cost_value' => $product->stock_quantity * $product->cost_price,
                    'total_selling_value' => $product->stock_quantity * $product->selling_price,
                    'potential_profit' => ($product->selling_price - $product->cost_price) * $product->stock_quantity,
                ];
            })
            ->sortByDesc('total_cost_value')
            ->values();
        
        $stats = [
            'total_products' => $products->count(),
            'total_stock_value_cost' => $products->sum('total_cost_value'),
            'total_stock_value_selling' => $products->sum('total_selling_value'),
            'total_potential_profit' => $products->sum('potential_profit'),
        ];
        
        return view('reports.inventory.stock-valuation', compact('products', 'stats'));
    }
    
    public function slowMovingProducts(Request $request)
    {
        $this->authorizePermission('view inventory-reports');
        $days = $request->get('days', 90); // Products not sold in last X days
        $dateThreshold = Carbon::now()->subDays($days);
        
        // Get all products with stock
        $allProducts = Product::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->with(['category', 'brand'])
            ->get();
        
        $slowMoving = collect();
        
        foreach ($allProducts as $product) {
            // Get last sale date
            $lastSale = SaleItem::where('product_id', $product->id)
                ->whereHas('sale', function($q) {
                    $q->where('status', 'completed');
                })
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->max('sales.sale_date');
            
            // Check if product is slow moving (no sale in last X days or never sold)
            if (!$lastSale || Carbon::parse($lastSale)->lt($dateThreshold)) {
                $daysSinceSale = $lastSale ? Carbon::parse($lastSale)->diffInDays(Carbon::now()) : null;
                
                $slowMoving->push([
                    'product' => $product,
                    'stock_value' => $product->stock_quantity * $product->cost_price,
                    'last_sale_date' => $lastSale ? Carbon::parse($lastSale)->format('d M Y') : 'Never',
                    'days_since_sale' => $daysSinceSale,
                ]);
            }
        }
        
        $slowMoving = $slowMoving->sortByDesc('stock_value')->values();
        
        return view('reports.inventory.slow-moving', compact('slowMoving', 'days'));
    }
    
    public function fastMovingProducts(Request $request)
    {
        $this->authorizePermission('view inventory-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $limit = $request->get('limit', 20);
        
        $fastMoving = SaleItem::whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'completed')
                  ->whereBetween('sale_date', [$dateFrom, $dateTo]);
            })
            ->select('product_id',
                     DB::raw('SUM(quantity) as total_sold'),
                     DB::raw('COUNT(DISTINCT sale_id) as sale_count'))
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
        
        return view('reports.inventory.fast-moving', compact('fastMoving', 'dateFrom', 'dateTo', 'limit'));
    }
    
    public function stockTurnover(Request $request)
    {
        $this->authorizePermission('view inventory-reports');
        $period = $request->get('period', 30); // Days
        
        $products = Product::where('status', 'active')
            ->with(['category'])
            ->get()
            ->map(function($product) use ($period) {
                // Get total sold in period
                $totalSold = SaleItem::where('product_id', $product->id)
                    ->whereHas('sale', function($q) use ($period) {
                        $q->where('status', 'completed')
                          ->where('sale_date', '>=', Carbon::now()->subDays($period));
                    })
                    ->sum('quantity');
                
                // Calculate turnover ratio
                $averageStock = $product->stock_quantity; // Simplified - should use average
                $turnoverRatio = $averageStock > 0 ? ($totalSold / $averageStock) : 0;
                
                return [
                    'product' => $product,
                    'average_stock' => $averageStock,
                    'sold_in_period' => $totalSold,
                    'turnover_ratio' => $turnoverRatio,
                    'days_to_turnover' => $turnoverRatio > 0 ? ($period / $turnoverRatio) : null,
                ];
            })
            ->sortByDesc('turnover_ratio')
            ->values();
        
        return view('reports.inventory.stock-turnover', compact('products', 'period'));
    }
    
    // ==================== EXPORT METHODS ====================
    // These will be similar to AccountingReportController export methods
    // I'll implement key exports for the main reports
    
    public function exportSalesReport(Request $request, $format)
    {
        $this->authorizePermission('export reports');
        // Similar export logic as accounting reports
        $period = $request->get('period', 'month');
        // ... implement export
    }
    
    // Additional export methods for each report type
}
