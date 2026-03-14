# ERP Software – Every Module, Page, and Option (Interconnected)

This note documents **every option** in the ERP: modules, routes, pages, actions, and how they connect to each other and to accounting.

---

## 1. Overview

- **Purpose**: Laptop/computer accessories reseller – sales, purchases, service orders, inventory, warranty, returns, payments, expenses, and double-entry accounting.
- **Auth**: Login, logout, profile (view/edit, password change). All app routes require `auth` middleware.
- **Access**: Permission-based (Spatie). Sidebar and actions respect `@can` / `@canany`.
- **Interconnection**: Sales, Purchases, Services, Payments, Returns, and Expenses **post to Accounting** (journal entries). Reports and Accounting read from the same data. Dashboard and Quick Actions link to all main modules.

---

## 2. Module List (Sidebar Order)

| # | Sidebar label      | Route prefix / main route | Controller(s) |
|---|--------------------|---------------------------|----------------|
| 0 | Dashboard          | `dashboard`               | DashboardController |
| 1 | Sales              | `sales.*`, `customers.*`   | SaleController, CustomerController |
| 2 | Purchases          | `purchases.*`, `suppliers.*`| PurchaseController, SupplierController |
| 3 | Service Orders     | `services.*`              | ServiceController |
| 4 | Payments           | `payments.*`              | PaymentController |
| 5 | Expenses           | `expenses.*`              | ExpenseController |
| 6 | Products & Stock   | `products.*`, `categories.*`, `brands.*`, `product-models.*`, `stock.*` | ProductController, CategoryController, BrandController, ProductModelController, StockController |
| 7 | Warranty           | `warranties.*`, `warranty-submissions.*` | WarrantyController, WarrantySubmissionController |
| 8 | Returns            | `sale-returns.*`, `purchase-returns.*`, `service-returns.*` | SaleReturnController, PurchaseReturnController, ServiceReturnController |
| 9 | Accounting         | `bank-accounts.*`, `accounts.*`, `journal-entries.*`, `accounting.*` | BankAccountController, AccountController, JournalEntryController, AccountingReportController |
|10 | Reports            | `reports.*`               | ReportsController |
|11 | Settings           | `company-info.*`          | CompanyInfoController |
|12 | Access Control     | `user-management.*`, `role-management.*`, `profile.*` | UserManagementController, RoleManagementController, ProfileController |

---

## 3. Every Route and Option (By Module)

### 3.1 Dashboard

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `dashboard`      | GET    | Main dashboard |

**Page options**: Stats (monthly revenue, purchases, profit, products, low stock, customers, suppliers, today’s sales), Sales chart (30 days), Payment status chart, Top selling products (links to product), Recent sales (links to sale show), Alerts (low stock → stock.low-stock, customer dues → sales index unpaid, supplier dues → purchases index unpaid, pending services → services, warranty submissions → warranty-submissions), **Quick Actions**: New Sale, New Purchase Order, New Service Order, Add Expense, Payments, Low Stock, Stock & Movement, Sales Report.

**Interconnections**: All quick actions and alerts link to the corresponding module. Data from Sales, Purchases, Products, Customers, Suppliers, Services, WarrantySubmissions.

---

### 3.2 Sales

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `sales.index`    | GET    | List invoices |
| `sales.create`   | GET    | New invoice form |
| `sales.store`    | POST   | Save new invoice |
| `sales.show`     | GET    | Invoice detail |
| `sales.edit`     | GET    | Edit invoice (draft) |
| `sales.update`   | PUT    | Update invoice |
| `sales.destroy`  | DELETE | Delete sale |
| `sales.complete` | POST   | Mark invoice completed (triggers accounting) |
| `sales.collect-payment` | POST | Collect payment (triggers accounting) |
| `sales.print`    | GET    | Print invoice |
| `sales.products-by-barcode` | GET | AJAX product by barcode |

**Customer routes**: `customers.index`, `customers.create`, `customers.store`, `customers.show`, `customers.edit`, `customers.update`, `customers.destroy`.

**Page options (sales)**:
- Index: filters (search, status, payment), New Invoice, row → show; Actions: Collect Payment, View, Edit, Print.
- Show: Complete Sale, Edit, Create Return, Print, Back, Collect Payment, Delete; customer info; line items; payments; related sale returns (link to sale-returns).
- Create/Edit: customer (optional link to customers), products (barcode), line items, totals.

**Accounting**: On **complete** and on **collect payment**, `AccountingService::recordSale($sale)` posts journal entry (AR/Cash, Sales Revenue 4000).  
**Interconnections**: Customer (belongs to or walk-in), SaleItem → Product, Payment (many), SaleReturn (many), JournalEntry (reference_type `sale`).

---

### 3.3 Purchases

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `purchases.index`   | GET    | List POs |
| `purchases.create`  | GET    | New PO form |
| `purchases.store`   | POST   | Save PO |
| `purchases.show`    | GET    | PO detail |
| `purchases.edit`    | GET    | Edit PO |
| `purchases.update`  | PUT    | Update PO |
| `purchases.destroy` | DELETE | Delete PO |
| `purchases.receive`  | GET    | Receive items page |
| `purchases.receive-item` | POST | Receive one line |
| `purchases.receive-multiple` | POST | Receive multiple lines |
| `purchases.collect-payment` | POST | Record supplier payment |
| `purchases.print`   | GET    | Print PO |

**Supplier routes**: `suppliers.index`, `suppliers.create`, `suppliers.store`, `suppliers.show`, `suppliers.edit`, `suppliers.update`, `suppliers.destroy`.

**Accounting**: On create/update (when received), `AccountingService::recordPurchase($purchase)` (Inventory 1300, AP 2000, Cash 1000). On `collect-payment`, `AccountingService::recordPayment($payment)`.  
**Interconnections**: Supplier, PurchaseItem → Product, Payment (many), PurchaseReturn (many), JournalEntry (reference_type `purchase`), Stock (receive updates stock).

---

### 3.4 Service Orders

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `services.index`    | GET    | List services |
| `services.create`   | GET    | New service form |
| `services.store`    | POST   | Save service |
| `services.show`     | GET    | Service detail |
| `services.edit`     | GET    | Edit service |
| `services.update`   | PUT    | Update service |
| `services.destroy`  | DELETE | Delete service |
| `services.print`    | GET    | Print service memo |
| `services.update-status` | PATCH | Quick status change (pending/in_progress/completed/delivered/cancelled) |
| `services.collect-payment` | POST | Collect payment on service |

**Accounting**: When status is **completed** or **delivered**, `AccountingService::recordService($service)` (Cash 1000, AR 1200, Service Revenue 4100). On status change, edit, collect payment: re-record or remove entry. On delete: `AccountingService::deleteJournalEntry('service', $id)`.  
**Interconnections**: ServiceReturn (many), BankAccount (optional), JournalEntry (reference_type `service`).

---

### 3.5 Payments

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `payments.index`    | GET    | List all payments |
| `payments.create`   | GET    | New payment (sale or purchase) |
| `payments.store`    | POST   | Save payment |
| `payments.show`     | GET    | Payment detail |
| `payments.edit`     | GET    | Edit payment |
| `payments.update`   | PUT    | Update payment |
| `payments.destroy`  | DELETE | Delete payment |
| `payments.quick`    | POST   | Quick payment (from sale/purchase context) |

**Accounting**: On store/update, `AccountingService::recordPayment($payment)` (Cash, AR or AP). On delete, journal entry removed.  
**Interconnections**: Payment belongs to Sale or Purchase; links to BankAccount; JournalEntry (reference_type `payment`).

---

### 3.6 Expenses

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `expenses.index`     | GET    | List expenses |
| `expenses.create`    | GET    | New expense |
| `expenses.store`     | POST   | Save expense |
| `expenses.show`      | GET    | Expense detail |
| `expenses.edit`      | GET    | Edit expense |
| `expenses.update`    | PUT    | Update expense |
| `expenses.destroy`   | DELETE | Delete expense |
| `expenses.approve`   | POST   | Approve expense |
| `expenses.mark-paid` | POST   | Mark as paid (triggers accounting) |
| `expenses.cancel`    | POST   | Cancel expense |

**Accounting**: On approve/paid, `AccountingService::recordExpense($expense)` (Expense account, Cash). Expense model observer also calls recordExpense on status change. Journal entry uses `reference_type` = Expense class.  
**Interconnections**: Expense → Account (chart of accounts), JournalEntry.

---

### 3.7 Products & Stock

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `categories.*` (resource) | CRUD | Categories |
| `brands.*` (resource)      | CRUD | Brands |
| `product-models.*` (resource) | CRUD | Product models |
| `products.models-by-brand` | GET | AJAX models by brand |
| `products.*` (resource)    | CRUD | Products |
| `products.adjust-stock`    | POST | Adjust product stock |
| `stock.index`              | GET  | Stock list & movements |
| `stock.low-stock`          | GET  | Low stock products |
| `stock.create-manual`      | GET  | Manual stock entry form |
| `stock.store-manual`       | POST | Save manual entry |

**Interconnections**: Product → Category, Brand, ProductModel; SaleItem/PurchaseItem/PurchaseReturnItem/SaleReturnItem → Product; StockMovement (reference: sale, purchase, manual, etc.); reports (sales by product, inventory reports).

---

### 3.8 Warranty

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `warranties.verify`        | GET    | Verify warranty (barcode input) |
| `warranties.verify-by-barcode` | POST | Lookup by barcode |
| `warranties.warranty-by-barcode` | GET | AJAX warranty by barcode |
| `warranties.index`         | GET    | List all warranties |
| `warranties.show`          | GET    | Warranty detail |
| `warranty-submissions.warranty-by-barcode` | GET | AJAX for submissions |
| `warranty-submissions.*` (resource) | CRUD + print | Submissions (create from warranty) |
| `warranty-submissions.print` | GET  | Print submission memo |

**Interconnections**: Warranty → Product (and sale); WarrantySubmission → Warranty. No accounting integration.

---

### 3.9 Returns

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `sale-returns.*` (resource) | CRUD | Sale returns |
| `sale-returns.approve`     | POST | Approve |
| `sale-returns.complete`   | POST | Complete (triggers accounting) |
| `purchase-returns.*` (resource) | CRUD | Purchase returns |
| `purchase-returns.approve` | POST | Approve |
| `purchase-returns.complete` | POST | Complete (triggers accounting) |
| `service-returns.*` (resource) | CRUD | Service returns |
| `service-returns.approve`  | POST | Approve |
| `service-returns.complete`| POST | Complete |
| `service-returns.process-refund` | POST | Process refund (triggers accounting) |

**Accounting**: Sale return → `recordSaleReturn` (Sales Revenue 4000, AR/Cash reversal). Purchase return → `recordPurchaseReturn` (Inventory, AP/Cash). Service return → `recordServiceReturn` (Service Revenue 4100, Cash).  
**Interconnections**: SaleReturn → Sale; PurchaseReturn → Purchase; ServiceReturn → Service; all post to JournalEntry.

---

### 3.10 Accounting

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `bank-accounts.*` (resource) | CRUD | Bank accounts |
| `bank-accounts.update-balance` | POST | Update balance |
| `accounts.*` (resource) | CRUD | Chart of accounts |
| `journal-entries.*` (resource) | CRUD | Journal entries |
| `journal-entries.post`   | POST | Post draft entry |
| `journal-entries.unpost` | POST | Unpost entry |
| `accounting.ledger`     | GET  | Ledger report |
| `accounting.trial-balance` | GET | Trial balance |
| `accounting.balance-sheet` | GET | Balance sheet |
| `accounting.profit-loss` | GET | P&L report |
| `accounting.ledger.export` etc. | GET | Export (csv/excel/pdf) |

**Interconnections**: Journal entries created by AccountingService from Sale, Purchase, Payment, Service, Expense, SaleReturn, PurchaseReturn, ServiceReturn. Each has `reference_type` and `reference_id` to link back to source document. Manual entries use reference_type `manual`.

---

### 3.11 Reports

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `reports.index`  | GET    | Reports hub (Overview) |
| `reports.sales.index`, `reports.sales.by-product`, `by-category`, `by-brand`, `by-customer`, `top-products` | GET | Sales reports |
| `reports.purchases.index`, `by-supplier`, `cost-analysis` | GET | Purchase reports |
| `reports.financial.profit-loss`, `gross-margin-product`, `gross-margin-category`, `cash-flow`, `accounts-receivable`, `accounts-payable` | GET | Financial reports |
| `reports.inventory.stock-valuation`, `slow-moving`, `fast-moving`, `stock-turnover` | GET | Inventory reports |

**Interconnections**: Read from Sales, Purchases, Products, Customers, Suppliers, Payments, Expenses, Stock. No write; all modules feed data to reports.

---

### 3.12 Settings

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `company-info.edit`  | GET  | Edit company info |
| `company-info.update`| PUT  | Save company info |

Used for invoice/PO headers, documents. No accounting link.

---

### 3.13 Access Control & Profile

| Route name       | Method | Purpose |
|------------------|--------|---------|
| `profile.show`, `profile.edit`, `profile.update`, `profile.password.update` | GET/PUT | Current user profile |
| `user-management.*` (resource) | CRUD | Users |
| `role-management.*` (resource) | CRUD | Roles (permissions) |

No accounting or operational data; permissions control access to all above modules.

---

## 4. Accounting Integration Summary (What Posts to Ledger)

| Source | reference_type | When | Accounts used |
|--------|----------------|------|----------------|
| Sale (completed) | `sale` | Complete sale, collect payment | 1000 Cash, 1200 AR, 4000 Sales Revenue |
| Purchase | `purchase` | Create/update PO (receive) | 1300 Inventory, 2000 AP, 1000 Cash |
| Payment | `payment` | Create/update payment | 1000 Cash, 1200 AR, 2000 AP |
| Service (completed/delivered) | `service` | Create/update/collect payment/status | 1000 Cash, 1200 AR, 4100 Service Revenue |
| Expense (approved/paid) | `App\Models\Expense` | Approve, mark paid | Expense account, 1000 Cash |
| Sale return (completed) | `sale_return` | Complete | 4000 Sales Revenue (debit), AR/Cash |
| Purchase return (completed) | `purchase_return` | Complete | 1300 Inventory, 2000 AP, 1000 Cash |
| Service return (completed) | `service_return` | Process refund | 4100 Service Revenue (debit), 1000 Cash (credit) |

---

## 5. Data Flow / Cross-Module Links

- **Dashboard** → Sales, Purchases, Services, Stock, Payments, Expenses, Reports, Customers/Suppliers (counts), Alerts (invoices unpaid, low stock, etc.).
- **Sales** → Customers, Products (items), Payments, Sale Returns, Accounting (journal).
- **Purchases** → Suppliers, Products (items), Receive → Stock, Payments, Purchase Returns, Accounting.
- **Services** → Service Returns, Accounting (service revenue).
- **Payments** → Sale or Purchase; Accounting.
- **Expenses** → Chart of Accounts (expense account); Accounting.
- **Returns** → Parent document (sale/purchase/service); Accounting.
- **Accounting** → Journal entries reference sale, purchase, payment, service, expense, returns; Ledger/Trial Balance/P&L/Balance Sheet read from journal.
- **Reports** → Read-only from sales, purchases, products, customers, suppliers, inventory, payments.

---

## 6. Backend Interconnection Checklist

- **AccountingService** is the single place that creates/updates/deletes journal entries for: Sale, Purchase, Payment, Service, Expense, SaleReturn, PurchaseReturn, ServiceReturn.
- **JournalEntry** stores `reference_type` and `reference_id` so every auto-generated entry can link back to its source (invoice, PO, service, payment, expense, return). The UI can show “View Invoice” / “View PO” etc. ; implemented: Journal Entries index and show display a clickable link to the source (getSourceUrl/getSourceLabel).
- **Payment** has `sale_id` or `purchase_id` → link from payment to invoice/PO and vice versa.
- **Sale/Purchase** show pages link to customer/supplier, returns, payments, and (if implemented) related journal entry.
- **Stock** movements reference sale, purchase, manual, return, etc., so inventory is traceable to transactions.
- **Permissions** are applied in controllers and views so every option is gated; sidebar shows only allowed modules and actions.

This document is the single reference for “every option” and how the software is interconnected end-to-end.
