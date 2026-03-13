<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductModelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\WarrantySubmissionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\ServiceReturnController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\AccountingReportController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Spatie\Permission\Models\Role;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });
    
    // Route Model Binding for User Management
    Route::bind('user_management', function ($value) {
        return User::findOrFail($value);
    });
    
    // Route Model Binding for Role Management
    Route::bind('role_management', function ($value) {
        return Role::findOrFail($value);
    });
    
    // User Management Routes
    Route::resource('user-management', UserManagementController::class)->parameters([
        'user-management' => 'user'
    ])->names([
        'index' => 'user-management.index',
        'create' => 'user-management.create',
        'store' => 'user-management.store',
        'show' => 'user-management.show',
        'edit' => 'user-management.edit',
        'update' => 'user-management.update',
        'destroy' => 'user-management.destroy',
    ]);
    
    // Role Management Routes
    Route::resource('role-management', RoleManagementController::class)->parameters([
        'role-management' => 'role'
    ])->names([
        'index' => 'role-management.index',
        'create' => 'role-management.create',
        'store' => 'role-management.store',
        'show' => 'role-management.show',
        'edit' => 'role-management.edit',
        'update' => 'role-management.update',
        'destroy' => 'role-management.destroy',
    ]);
    
    // Service Management Routes
    Route::resource('services', ServiceController::class)->names([
        'index' => 'services.index',
        'create' => 'services.create',
        'store' => 'services.store',
        'show' => 'services.show',
        'edit' => 'services.edit',
        'update' => 'services.update',
        'destroy' => 'services.destroy',
    ]);
    
    // Service Print Route
    Route::get('services/{service}/print', [ServiceController::class, 'print'])->name('services.print');
    
    // Quick Status Update Route
    Route::patch('services/{service}/status', [ServiceController::class, 'updateStatus'])->name('services.update-status');

    // Collect Payment Route
    Route::post('services/{service}/collect-payment', [ServiceController::class, 'collectPayment'])->name('services.collect-payment');

    // Sale Collect Payment Route
    Route::post('sales/{sale}/collect-payment', [SaleController::class, 'collectPayment'])->name('sales.collect-payment');

    // Purchase Collect Payment Route
    Route::post('purchases/{purchase}/collect-payment', [PurchaseController::class, 'collectPayment'])->name('purchases.collect-payment');
    
    // Settings Routes
    Route::get('settings/company-info', [CompanyInfoController::class, 'edit'])->name('company-info.edit');
    Route::put('settings/company-info', [CompanyInfoController::class, 'update'])->name('company-info.update');
    
    // Product Management Routes
    Route::resource('categories', CategoryController::class)->names([
        'index' => 'categories.index',
        'create' => 'categories.create',
        'store' => 'categories.store',
        'show' => 'categories.show',
        'edit' => 'categories.edit',
        'update' => 'categories.update',
        'destroy' => 'categories.destroy',
    ]);
    
    Route::resource('brands', BrandController::class)->names([
        'index' => 'brands.index',
        'create' => 'brands.create',
        'store' => 'brands.store',
        'show' => 'brands.show',
        'edit' => 'brands.edit',
        'update' => 'brands.update',
        'destroy' => 'brands.destroy',
    ]);
    
    Route::resource('product-models', ProductModelController::class)->names([
        'index' => 'product-models.index',
        'create' => 'product-models.create',
        'store' => 'product-models.store',
        'show' => 'product-models.show',
        'edit' => 'product-models.edit',
        'update' => 'product-models.update',
        'destroy' => 'product-models.destroy',
    ]);
    
    // This route must be BEFORE the products resource route to avoid conflicts
    Route::get('products/models-by-brand', [ProductController::class, 'getModelsByBrand'])->name('products.models-by-brand');
    
    Route::resource('products', ProductController::class)->names([
        'index' => 'products.index',
        'create' => 'products.create',
        'store' => 'products.store',
        'show' => 'products.show',
        'edit' => 'products.edit',
        'update' => 'products.update',
        'destroy' => 'products.destroy',
    ]);
    
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/low-stock', [StockController::class, 'lowStock'])->name('stock.low-stock');
    Route::get('stock/manual-entry', [StockController::class, 'createManual'])->name('stock.create-manual');
    Route::post('stock/manual-entry', [StockController::class, 'storeManual'])->name('stock.store-manual');
    Route::post('products/{product}/adjust-stock', [StockController::class, 'adjustStock'])->name('products.adjust-stock');
    
    // Purchase Management Routes
    Route::resource('suppliers', SupplierController::class)->names([
        'index' => 'suppliers.index',
        'create' => 'suppliers.create',
        'store' => 'suppliers.store',
        'show' => 'suppliers.show',
        'edit' => 'suppliers.edit',
        'update' => 'suppliers.update',
        'destroy' => 'suppliers.destroy',
    ]);
    
    Route::resource('purchases', PurchaseController::class)->names([
        'index' => 'purchases.index',
        'create' => 'purchases.create',
        'store' => 'purchases.store',
        'show' => 'purchases.show',
        'edit' => 'purchases.edit',
        'update' => 'purchases.update',
        'destroy' => 'purchases.destroy',
    ]);
    
    Route::get('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('purchases/{purchase}/items/{item}/receive', [PurchaseController::class, 'receiveItem'])->name('purchases.receive-item');
    Route::post('purchases/{purchase}/receive-multiple', [PurchaseController::class, 'receiveMultipleItems'])->name('purchases.receive-multiple');
    Route::get('purchases/{purchase}/print', [PurchaseController::class, 'printInvoice'])->name('purchases.print');
    
    // Sales Management Routes
    Route::resource('customers', CustomerController::class)->names([
        'index' => 'customers.index',
        'create' => 'customers.create',
        'store' => 'customers.store',
        'show' => 'customers.show',
        'edit' => 'customers.edit',
        'update' => 'customers.update',
        'destroy' => 'customers.destroy',
    ]);
    
    // Sales routes - specific routes before resource route
    Route::get('sales/products/by-barcode', [SaleController::class, 'getProductByBarcode'])->name('sales.products-by-barcode');
    
    Route::resource('sales', SaleController::class)->names([
        'index' => 'sales.index',
        'create' => 'sales.create',
        'store' => 'sales.store',
        'show' => 'sales.show',
        'edit' => 'sales.edit',
        'update' => 'sales.update',
        'destroy' => 'sales.destroy',
    ]);
    
    Route::post('sales/{sale}/complete', [SaleController::class, 'complete'])->name('sales.complete');
    Route::get('sales/{sale}/print', [SaleController::class, 'printInvoice'])->name('sales.print');
    
    // Warranty Management Routes
    Route::get('warranties/verify', [WarrantyController::class, 'verify'])->name('warranties.verify');
    Route::post('warranties/verify', [WarrantyController::class, 'verifyByBarcode'])->name('warranties.verify-by-barcode');
    Route::get('warranties/warranty-by-barcode', [WarrantyController::class, 'getWarrantyByBarcode'])->name('warranties.warranty-by-barcode');
    Route::resource('warranties', WarrantyController::class)->only(['index', 'show'])->names([
        'index' => 'warranties.index',
        'show' => 'warranties.show',
    ]);
    
    // Warranty Submission Routes - specific routes before resource
    Route::get('warranty-submissions/warranty-by-barcode', [WarrantySubmissionController::class, 'getWarrantyByBarcode'])->name('warranty-submissions.warranty-by-barcode');
    Route::get('warranty-submissions/{warrantySubmission}/print', [WarrantySubmissionController::class, 'printMemo'])->name('warranty-submissions.print');
    
    Route::resource('warranty-submissions', WarrantySubmissionController::class)->names([
        'index' => 'warranty-submissions.index',
        'create' => 'warranty-submissions.create',
        'store' => 'warranty-submissions.store',
        'show' => 'warranty-submissions.show',
        'edit' => 'warranty-submissions.edit',
        'update' => 'warranty-submissions.update',
        'destroy' => 'warranty-submissions.destroy',
    ]);
    
    // Payment Management Routes
    Route::post('payments/quick', [PaymentController::class, 'quickPayment'])->name('payments.quick');
    Route::resource('payments', PaymentController::class)->names([
        'index' => 'payments.index',
        'create' => 'payments.create',
        'store' => 'payments.store',
        'show' => 'payments.show',
        'edit' => 'payments.edit',
        'update' => 'payments.update',
        'destroy' => 'payments.destroy',
    ]);
    
    // Return Management Routes
    Route::post('purchase-returns/{purchaseReturn}/approve', [PurchaseReturnController::class, 'approve'])->name('purchase-returns.approve');
    Route::post('purchase-returns/{purchaseReturn}/complete', [PurchaseReturnController::class, 'complete'])->name('purchase-returns.complete');
    Route::resource('purchase-returns', PurchaseReturnController::class)->names([
        'index' => 'purchase-returns.index',
        'create' => 'purchase-returns.create',
        'store' => 'purchase-returns.store',
        'show' => 'purchase-returns.show',
        'edit' => 'purchase-returns.edit',
        'update' => 'purchase-returns.update',
        'destroy' => 'purchase-returns.destroy',
    ]);
    
    Route::post('sale-returns/{saleReturn}/approve', [SaleReturnController::class, 'approve'])->name('sale-returns.approve');
    Route::post('sale-returns/{saleReturn}/complete', [SaleReturnController::class, 'complete'])->name('sale-returns.complete');
    Route::resource('sale-returns', SaleReturnController::class)->names([
        'index' => 'sale-returns.index',
        'create' => 'sale-returns.create',
        'store' => 'sale-returns.store',
        'show' => 'sale-returns.show',
        'edit' => 'sale-returns.edit',
        'update' => 'sale-returns.update',
        'destroy' => 'sale-returns.destroy',
    ]);
    
    Route::post('service-returns/{serviceReturn}/approve', [ServiceReturnController::class, 'approve'])->name('service-returns.approve');
    Route::post('service-returns/{serviceReturn}/complete', [ServiceReturnController::class, 'complete'])->name('service-returns.complete');
    Route::post('service-returns/{serviceReturn}/process-refund', [ServiceReturnController::class, 'processRefund'])->name('service-returns.process-refund');
    Route::resource('service-returns', ServiceReturnController::class)->names([
        'index' => 'service-returns.index',
        'create' => 'service-returns.create',
        'store' => 'service-returns.store',
        'show' => 'service-returns.show',
        'edit' => 'service-returns.edit',
        'update' => 'service-returns.update',
        'destroy' => 'service-returns.destroy',
    ]);
    
    // Accounting Management Routes
    Route::resource('bank-accounts', BankAccountController::class)->names([
        'index' => 'bank-accounts.index',
        'create' => 'bank-accounts.create',
        'store' => 'bank-accounts.store',
        'show' => 'bank-accounts.show',
        'edit' => 'bank-accounts.edit',
        'update' => 'bank-accounts.update',
        'destroy' => 'bank-accounts.destroy',
    ]);
    Route::post('bank-accounts/{bankAccount}/update-balance', [BankAccountController::class, 'updateBalance'])->name('bank-accounts.update-balance');
    
    Route::resource('accounts', AccountController::class)->names([
        'index' => 'accounts.index',
        'create' => 'accounts.create',
        'store' => 'accounts.store',
        'show' => 'accounts.show',
        'edit' => 'accounts.edit',
        'update' => 'accounts.update',
        'destroy' => 'accounts.destroy',
    ]);
    
    Route::post('journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post');
    Route::post('journal-entries/{journalEntry}/unpost', [JournalEntryController::class, 'unpost'])->name('journal-entries.unpost');
    Route::resource('journal-entries', JournalEntryController::class)->names([
        'index' => 'journal-entries.index',
        'create' => 'journal-entries.create',
        'store' => 'journal-entries.store',
        'show' => 'journal-entries.show',
        'edit' => 'journal-entries.edit',
        'update' => 'journal-entries.update',
        'destroy' => 'journal-entries.destroy',
    ]);
    
    // Expense Management Routes
    Route::resource('expenses', ExpenseController::class)->names([
        'index' => 'expenses.index',
        'create' => 'expenses.create',
        'store' => 'expenses.store',
        'show' => 'expenses.show',
        'edit' => 'expenses.edit',
        'update' => 'expenses.update',
        'destroy' => 'expenses.destroy',
    ]);
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/mark-paid', [ExpenseController::class, 'markAsPaid'])->name('expenses.mark-paid');
    Route::post('expenses/{expense}/cancel', [ExpenseController::class, 'cancel'])->name('expenses.cancel');
    
    Route::get('accounting/ledger', [AccountingReportController::class, 'ledger'])->name('accounting.ledger');
    Route::get('accounting/trial-balance', [AccountingReportController::class, 'trialBalance'])->name('accounting.trial-balance');
    Route::get('accounting/balance-sheet', [AccountingReportController::class, 'balanceSheet'])->name('accounting.balance-sheet');
    Route::get('accounting/profit-loss', [AccountingReportController::class, 'profitLoss'])->name('accounting.profit-loss');
    
    // Accounting Export Routes
    Route::get('accounting/ledger/export/{format}', [AccountingReportController::class, 'exportLedger'])->name('accounting.ledger.export');
    Route::get('accounting/trial-balance/export/{format}', [AccountingReportController::class, 'exportTrialBalance'])->name('accounting.trial-balance.export');
    Route::get('accounting/balance-sheet/export/{format}', [AccountingReportController::class, 'exportBalanceSheet'])->name('accounting.balance-sheet.export');
    Route::get('accounting/profit-loss/export/{format}', [AccountingReportController::class, 'exportProfitLoss'])->name('accounting.profit-loss.export');
    
    // Reports & Analytics Routes
    // Sales Reports
    Route::get('reports/sales', [ReportsController::class, 'salesReport'])->name('reports.sales.index');
    Route::get('reports/sales/by-product', [ReportsController::class, 'salesByProduct'])->name('reports.sales.by-product');
    Route::get('reports/sales/by-category', [ReportsController::class, 'salesByCategory'])->name('reports.sales.by-category');
    Route::get('reports/sales/by-brand', [ReportsController::class, 'salesByBrand'])->name('reports.sales.by-brand');
    Route::get('reports/sales/by-customer', [ReportsController::class, 'salesByCustomer'])->name('reports.sales.by-customer');
    Route::get('reports/sales/top-products', [ReportsController::class, 'topSellingProducts'])->name('reports.sales.top-products');
    
    // Purchase Reports
    Route::get('reports/purchases', [ReportsController::class, 'purchaseReport'])->name('reports.purchases.index');
    Route::get('reports/purchases/by-supplier', [ReportsController::class, 'purchasesBySupplier'])->name('reports.purchases.by-supplier');
    Route::get('reports/purchases/cost-analysis', [ReportsController::class, 'costAnalysis'])->name('reports.purchases.cost-analysis');
    
    // Financial Reports
    Route::get('reports/financial/profit-loss', [ReportsController::class, 'profitLossReport'])->name('reports.financial.profit-loss');
    Route::get('reports/financial/gross-margin/product', [ReportsController::class, 'grossMarginByProduct'])->name('reports.financial.gross-margin-product');
    Route::get('reports/financial/gross-margin/category', [ReportsController::class, 'grossMarginByCategory'])->name('reports.financial.gross-margin-category');
    Route::get('reports/financial/cash-flow', [ReportsController::class, 'cashFlowReport'])->name('reports.financial.cash-flow');
    Route::get('reports/financial/accounts-receivable', [ReportsController::class, 'accountsReceivable'])->name('reports.financial.accounts-receivable');
    Route::get('reports/financial/accounts-payable', [ReportsController::class, 'accountsPayable'])->name('reports.financial.accounts-payable');
    
    // Inventory Reports
    Route::get('reports/inventory/stock-valuation', [ReportsController::class, 'stockValuation'])->name('reports.inventory.stock-valuation');
    Route::get('reports/inventory/slow-moving', [ReportsController::class, 'slowMovingProducts'])->name('reports.inventory.slow-moving');
    Route::get('reports/inventory/fast-moving', [ReportsController::class, 'fastMovingProducts'])->name('reports.inventory.fast-moving');
    Route::get('reports/inventory/stock-turnover', [ReportsController::class, 'stockTurnover'])->name('reports.inventory.stock-turnover');
});
