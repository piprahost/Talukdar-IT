# Permissions Implementation Guide

## Overview
This application uses Spatie Laravel Permission package for role-based access control (RBAC).

## Permission Naming Convention
Permissions follow the pattern: `{action} {resource}`
- Actions: view, create, edit, delete, plus module-specific actions
- Resources: Module names (e.g., products, sales, purchases, services)

## Controller Permission Mapping

### Product Management
- `index()` → `view products`
- `create()` → `create products`
- `store()` → `create products`
- `show()` → `view products`
- `edit()` → `edit products`
- `update()` → `edit products`
- `destroy()` → `delete products`

### Sales Management
- `index()` → `view sales`
- `create()` → `create sales`
- `store()` → `create sales`
- `show()` → `view sales`
- `edit()` → `edit sales`
- `update()` → `edit sales`
- `destroy()` → `delete sales`
- `complete()` → `complete sales`
- `printInvoice()` → `print invoices`

### Purchase Management
- `index()` → `view purchases`
- `create()` → `create purchases`
- `store()` → `create purchases`
- `show()` → `view purchases`
- `edit()` → `edit purchases`
- `update()` → `edit purchases`
- `destroy()` → `delete purchases`
- `receive()` → `receive purchases`
- `printInvoice()` → `print purchase-invoices`

### Service Management
- `index()` → `view services`
- `create()` → `create services`
- `store()` → `create services`
- `show()` → `view services`
- `edit()` → `edit services`
- `update()` → `edit services`
- `destroy()` → `delete services`
- `updateStatus()` → `update service-status`
- `print()` → `print service-memos`

### Stock Management
- `index()` → `view stock-movements`
- `createManual()` → `create stock`
- `storeManual()` → `create stock`
- `adjustStock()` → `adjust stock`
- `lowStock()` → `view stock`

### Category Management
- `index()` → `view categories`
- `create()` → `create categories`
- `store()` → `create categories`
- `show()` → `view categories`
- `edit()` → `edit categories`
- `update()` → `edit categories`
- `destroy()` → `delete categories`

### Brand Management
- `index()` → `view brands`
- `create()` → `create brands`
- `store()` → `create brands`
- `show()` → `view brands`
- `edit()` → `edit brands`
- `update()` → `edit brands`
- `destroy()` → `delete brands`

### Product Model Management
- `index()` → `view product-models`
- `create()` → `create product-models`
- `store()` → `create product-models`
- `show()` → `view product-models`
- `edit()` → `edit product-models`
- `update()` → `edit product-models`
- `destroy()` → `delete product-models`

### Supplier Management
- `index()` → `view suppliers`
- `create()` → `create suppliers`
- `store()` → `create suppliers`
- `show()` → `view suppliers`
- `edit()` → `edit suppliers`
- `update()` → `edit suppliers`
- `destroy()` → `delete suppliers`

### Customer Management
- `index()` → `view customers`
- `create()` → `create customers`
- `store()` → `create customers`
- `show()` → `view customers`
- `edit()` → `edit customers`
- `update()` → `edit customers`
- `destroy()` → `delete customers`

### Warranty Management
- `index()` → `view warranties`
- `show()` → `view warranties`
- `verify()` → `verify warranties`
- `verifyByBarcode()` → `verify warranties`

### Warranty Submission Management
- `index()` → `view warranty-submissions`
- `create()` → `create warranty-submissions`
- `store()` → `create warranty-submissions`
- `show()` → `view warranty-submissions`
- `edit()` → `edit warranty-submissions`
- `update()` → `edit warranty-submissions`
- `destroy()` → `delete warranty-submissions`
- `printMemo()` → `print warranty-memos`

### Payment Management
- `index()` → `view payments`
- `create()` → `create payments`
- `store()` → `create payments`
- `show()` → `view payments`
- `edit()` → `edit payments`
- `update()` → `edit payments`
- `destroy()` → `delete payments`
- `quickPayment()` → `create payments`

### Purchase Return Management
- `index()` → `view purchase-returns`
- `create()` → `create purchase-returns`
- `store()` → `create purchase-returns`
- `show()` → `view purchase-returns`
- `edit()` → `edit purchase-returns`
- `update()` → `edit purchase-returns`
- `destroy()` → `delete purchase-returns`
- `approve()` → `approve purchase-returns`
- `complete()` → `complete purchase-returns`

### Sale Return Management
- `index()` → `view sale-returns`
- `create()` → `create sale-returns`
- `store()` → `create sale-returns`
- `show()` → `view sale-returns`
- `edit()` → `edit sale-returns`
- `update()` → `edit sale-returns`
- `destroy()` → `delete sale-returns`
- `approve()` → `approve sale-returns`
- `complete()` → `complete sale-returns`

### Service Return Management
- `index()` → `view service-returns`
- `create()` → `create service-returns`
- `store()` → `create service-returns`
- `show()` → `view service-returns`
- `edit()` → `edit service-returns`
- `update()` → `edit service-returns`
- `destroy()` → `delete service-returns`
- `approve()` → `approve service-returns`
- `complete()` → `complete service-returns`
- `processRefund()` → `process service-refunds`

### Bank Account Management
- `index()` → `view bank-accounts`
- `create()` → `create bank-accounts`
- `store()` → `create bank-accounts`
- `show()` → `view bank-accounts`
- `edit()` → `edit bank-accounts`
- `update()` → `edit bank-accounts`
- `destroy()` → `delete bank-accounts`
- `updateBalance()` → `update bank-balances`

### Chart of Accounts Management
- `index()` → `view accounts`
- `create()` → `create accounts`
- `store()` → `create accounts`
- `show()` → `view accounts`
- `edit()` → `edit accounts`
- `update()` → `edit accounts`
- `destroy()` → `delete accounts`

### Journal Entry Management
- `index()` → `view journal-entries`
- `create()` → `create journal-entries`
- `store()` → `create journal-entries`
- `show()` → `view journal-entries`
- `edit()` → `edit journal-entries`
- `update()` → `edit journal-entries`
- `destroy()` → `delete journal-entries`
- `post()` → `post journal-entries`
- `unpost()` → `unpost journal-entries`

### Expense Management
- `index()` → `view expenses`
- `create()` → `create expenses`
- `store()` → `create expenses`
- `show()` → `view expenses`
- `edit()` → `edit expenses`
- `update()` → `edit expenses`
- `destroy()` → `delete expenses`
- `approve()` → `approve expenses`
- `markAsPaid()` → `mark-expenses-paid`
- `cancel()` → `cancel expenses`

### Accounting Reports
- `ledger()` → `view accounting-reports`
- `trialBalance()` → `view accounting-reports`
- `balanceSheet()` → `view accounting-reports`
- `profitLoss()` → `view accounting-reports`
- `exportLedger()` → `export accounting-reports`
- `exportTrialBalance()` → `export accounting-reports`
- `exportBalanceSheet()` → `export accounting-reports`
- `exportProfitLoss()` → `export accounting-reports`

### Reports & Analytics
- `salesReport()` → `view sales-reports`
- `salesByProduct()` → `view sales-reports`
- `salesByCategory()` → `view sales-reports`
- `salesByBrand()` → `view sales-reports`
- `salesByCustomer()` → `view sales-reports`
- `topSellingProducts()` → `view sales-reports`
- `purchaseReport()` → `view purchase-reports`
- `purchasesBySupplier()` → `view purchase-reports`
- `costAnalysis()` → `view purchase-reports`
- `profitLossReport()` → `view financial-reports`
- `grossMarginByProduct()` → `view financial-reports`
- `grossMarginByCategory()` → `view financial-reports`
- `cashFlowReport()` → `view financial-reports`
- `accountsReceivable()` → `view financial-reports`
- `accountsPayable()` → `view financial-reports`
- `stockValuation()` → `view inventory-reports`
- `slowMovingProducts()` → `view inventory-reports`
- `fastMovingProducts()` → `view inventory-reports`
- `stockTurnover()` → `view inventory-reports`

### User Management
- `index()` → `view users`
- `create()` → `create users`
- `store()` → `create users`
- `show()` → `view users`
- `edit()` → `edit users`
- `update()` → `edit users`
- `destroy()` → `delete users`

### Role Management
- `index()` → `view roles`
- `create()` → `create roles`
- `store()` → `create roles`
- `show()` → `view roles`
- `edit()` → `edit roles`
- `update()` → `edit roles`
- `destroy()` → `delete roles`

### Settings
- `edit()` → `view settings`
- `update()` → `edit settings`

## Implementation Pattern

In each controller method, add:
```php
$this->authorizePermission('permission-name');
```

## View Implementation

In Blade templates, use:
```blade
@can('permission-name')
    <!-- Content that requires permission -->
@endcan

@cannot('permission-name')
    <!-- Content shown when user doesn't have permission -->
@endcannot
```

For buttons/links:
```blade
@can('create products')
    <a href="{{ route('products.create') }}" class="btn btn-primary">Create</a>
@endcan
```

## Route Middleware (Optional)

You can also add permission middleware to routes:
```php
Route::get('products', [ProductController::class, 'index'])
    ->middleware('permission:view products');
```

## Testing Permissions

After seeding:
1. Assign roles to users
2. Test each permission by accessing routes
3. Verify views show/hide correctly based on permissions

## Running Seeder

To update permissions, run:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

This will:
- Create all permissions if they don't exist
- Update default roles with their permissions
- Reset permission cache

