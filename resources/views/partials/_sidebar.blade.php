<!-- Sidebar: Ordered for daily operations first, admin last -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-store" style="font-size: 28px; color: #ffffff;"></i>
        <h4 class="mb-0">{{ isset($companySettings) && $companySettings ? $companySettings->company_name : 'Shop' }}</h4>
        @if(isset($companySettings) && $companySettings && !empty($companySettings->tagline))
        <p class="small mb-0 mt-1 opacity-75" style="font-size: 11px; line-height: 1.3;">{{ $companySettings->tagline }}</p>
        @endif
    </div>

    <nav class="sidebar-menu">
        @can('view dashboard')
        <a href="{{ route('dashboard') }}" class="sidebar-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        @endcan

        {{-- 1. Sales (daily ops) --}}
        @canany(['view sales', 'view customers'])
        <div class="sidebar-menu-group {{ request()->routeIs('sales.*') || request()->routeIs('customers.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'sales-management')">
                <i class="fas fa-cash-register"></i>
                <span>Sales</span>
                <i class="fas fa-chevron-{{ request()->routeIs('sales.*') || request()->routeIs('customers.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-sales-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('sales.*') || request()->routeIs('customers.*') ? 'show' : '' }}" id="submenu-sales-management">
                @can('view sales')
                <a href="{{ route('sales.index') }}" class="sidebar-submenu-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Invoices</span>
                </a>
                @endcan
                @can('view customers')
                <a href="{{ route('customers.index') }}" class="sidebar-submenu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- 2. Service (laptop / repair) — "Sales & Service" --}}
        @can('view services')
        <a href="{{ route('services.index') }}" class="sidebar-menu-item {{ request()->routeIs('services.*') ? 'active' : '' }}">
            <i class="fas fa-laptop-medical"></i>
            <span>Laptop Service</span>
        </a>
        @endcan

        {{-- 3. Purchases (stock in) --}}
        @canany(['view purchases', 'view suppliers'])
        <div class="sidebar-menu-group {{ request()->routeIs('purchases.*') || request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'purchase-management')">
                <i class="fas fa-shopping-cart"></i>
                <span>Purchases</span>
                <i class="fas fa-chevron-{{ request()->routeIs('purchases.*') || request()->routeIs('suppliers.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-purchase-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('purchases.*') || request()->routeIs('suppliers.*') ? 'show' : '' }}" id="submenu-purchase-management">
                @can('view purchases')
                <a href="{{ route('purchases.index') }}" class="sidebar-submenu-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Purchase Orders</span>
                </a>
                @endcan
                @can('view suppliers')
                <a href="{{ route('suppliers.index') }}" class="sidebar-submenu-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>Suppliers</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- 4. Payments --}}
        @can('view payments')
        <a href="{{ route('payments.index') }}" class="sidebar-menu-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave"></i>
            <span>Payments</span>
        </a>
        @endcan

        {{-- 5. Expenses --}}
        @can('view expenses')
        <a href="{{ route('expenses.index') }}" class="sidebar-menu-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>Expenses</span>
        </a>
        @endcan

        {{-- 6. Products & Stock (Manual Entry is on Stock page) --}}
        @canany(['view products', 'view categories', 'view brands', 'view product-models', 'view stock', 'view stock-movements'])
        <div class="sidebar-menu-group {{ request()->routeIs('categories.*') || request()->routeIs('brands.*') || request()->routeIs('product-models.*') || request()->routeIs('products.*') || request()->routeIs('stock.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'product-management')">
                <i class="fas fa-boxes"></i>
                <span>Products & Stock</span>
                <i class="fas fa-chevron-{{ request()->routeIs('categories.*') || request()->routeIs('brands.*') || request()->routeIs('product-models.*') || request()->routeIs('products.*') || request()->routeIs('stock.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-product-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('categories.*') || request()->routeIs('brands.*') || request()->routeIs('product-models.*') || request()->routeIs('products.*') || request()->routeIs('stock.*') ? 'show' : '' }}" id="submenu-product-management">
                @can('view products')
                <a href="{{ route('products.index') }}" class="sidebar-submenu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                @endcan
                @can('view stock-movements')
                <a href="{{ route('stock.index') }}" class="sidebar-submenu-item {{ request()->routeIs('stock.index') || request()->routeIs('stock.low-stock') || request()->routeIs('stock.create-manual') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i>
                    <span>Stock & Barcode</span>
                </a>
                @endcan
                @can('view categories')
                <a href="{{ route('categories.index') }}" class="sidebar-submenu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                @endcan
                @can('view brands')
                <a href="{{ route('brands.index') }}" class="sidebar-submenu-item {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                    <i class="fas fa-certificate"></i>
                    <span>Brands</span>
                </a>
                @endcan
                @can('view product-models')
                <a href="{{ route('product-models.index') }}" class="sidebar-submenu-item {{ request()->routeIs('product-models.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i>
                    <span>Models</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- 7. Warranty --}}
        @canany(['view warranties', 'verify warranties', 'view warranty-submissions'])
        <div class="sidebar-menu-group {{ request()->routeIs('warranties.*') || request()->routeIs('warranty-submissions.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'warranty-management')">
                <i class="fas fa-shield-alt"></i>
                <span>Warranty</span>
                <i class="fas fa-chevron-{{ request()->routeIs('warranties.*') || request()->routeIs('warranty-submissions.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-warranty-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('warranties.*') || request()->routeIs('warranty-submissions.*') ? 'show' : '' }}" id="submenu-warranty-management">
                @can('verify warranties')
                <a href="{{ route('warranties.verify') }}" class="sidebar-submenu-item {{ request()->routeIs('warranties.verify') ? 'active' : '' }}">
                    <i class="fas fa-search"></i>
                    <span>Verify Warranty</span>
                </a>
                @endcan
                @can('view warranties')
                <a href="{{ route('warranties.index') }}" class="sidebar-submenu-item {{ request()->routeIs('warranties.index') || request()->routeIs('warranties.show') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>All Warranties</span>
                </a>
                @endcan
                @can('view warranty-submissions')
                <a href="{{ route('warranty-submissions.index') }}" class="sidebar-submenu-item {{ request()->routeIs('warranty-submissions.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Submissions</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- 8. Returns --}}
        @canany(['view purchase-returns', 'view sale-returns', 'view service-returns'])
        <div class="sidebar-menu-group {{ request()->routeIs('purchase-returns.*') || request()->routeIs('sale-returns.*') || request()->routeIs('service-returns.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'return-management')">
                <i class="fas fa-undo"></i>
                <span>Returns</span>
                <i class="fas fa-chevron-{{ request()->routeIs('purchase-returns.*') || request()->routeIs('sale-returns.*') || request()->routeIs('service-returns.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-return-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('purchase-returns.*') || request()->routeIs('sale-returns.*') || request()->routeIs('service-returns.*') ? 'show' : '' }}" id="submenu-return-management">
                @can('view sale-returns')
                <a href="{{ route('sale-returns.index') }}" class="sidebar-submenu-item {{ request()->routeIs('sale-returns.*') ? 'active' : '' }}">
                    <i class="fas fa-undo"></i>
                    <span>Sale Returns</span>
                </a>
                @endcan
                @can('view purchase-returns')
                <a href="{{ route('purchase-returns.index') }}" class="sidebar-submenu-item {{ request()->routeIs('purchase-returns.*') ? 'active' : '' }}">
                    <i class="fas fa-undo"></i>
                    <span>Purchase Returns</span>
                </a>
                @endcan
                @can('view service-returns')
                <a href="{{ route('service-returns.index') }}" class="sidebar-submenu-item {{ request()->routeIs('service-returns.*') ? 'active' : '' }}">
                    <i class="fas fa-undo"></i>
                    <span>Service Returns</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- 9. Accounting --}}
        @canany(['view bank-accounts', 'view accounts', 'view journal-entries', 'view accounting-reports'])
        <div class="sidebar-menu-group {{ request()->routeIs('bank-accounts.*') || request()->routeIs('accounts.*') || request()->routeIs('journal-entries.*') || request()->routeIs('accounting.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'accounting')">
                <i class="fas fa-calculator"></i>
                <span>Accounting</span>
                <i class="fas fa-chevron-{{ request()->routeIs('bank-accounts.*') || request()->routeIs('accounts.*') || request()->routeIs('journal-entries.*') || request()->routeIs('accounting.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-accounting"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('bank-accounts.*') || request()->routeIs('accounts.*') || request()->routeIs('journal-entries.*') || request()->routeIs('accounting.*') ? 'show' : '' }}" id="submenu-accounting">
                @can('view bank-accounts')
                <a href="{{ route('bank-accounts.index') }}" class="sidebar-submenu-item {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}">
                    <i class="fas fa-university"></i>
                    <span>Bank Accounts</span>
                </a>
                @endcan
                @can('view accounting-reports')
                <a href="{{ route('accounting.profit-loss') }}" class="sidebar-submenu-item {{ request()->routeIs('accounting.profit-loss') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>P&L Report</span>
                </a>
                <a href="{{ route('accounting.balance-sheet') }}" class="sidebar-submenu-item {{ request()->routeIs('accounting.balance-sheet') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Balance Sheet</span>
                </a>
                <a href="{{ route('accounting.ledger') }}" class="sidebar-submenu-item {{ request()->routeIs('accounting.ledger') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Ledger</span>
                </a>
                <a href="{{ route('accounting.trial-balance') }}" class="sidebar-submenu-item {{ request()->routeIs('accounting.trial-balance') ? 'active' : '' }}">
                    <i class="fas fa-balance-scale"></i>
                    <span>Trial Balance</span>
                </a>
                @endcan
                @can('view accounts')
                <a href="{{ route('accounts.index') }}" class="sidebar-submenu-item {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Chart of Accounts</span>
                </a>
                @endcan
                @can('view journal-entries')
                <a href="{{ route('journal-entries.index') }}" class="sidebar-submenu-item {{ request()->routeIs('journal-entries.*') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i>
                    <span>Journal Entries</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- 10. Reports (hub + daily Sales Report only in sidebar) --}}
        @canany(['view sales-reports', 'view purchase-reports', 'view financial-reports', 'view inventory-reports'])
        <a href="{{ route('reports.index') }}" class="sidebar-menu-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        @can('view sales-reports')
        <a href="{{ route('reports.sales.index') }}" class="sidebar-menu-item {{ request()->routeIs('reports.sales.index') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Sales Report</span>
        </a>
        @endcan
        @endcanany

        {{-- 11. Settings --}}
        @can('view settings')
        <div class="sidebar-menu-group {{ request()->routeIs('company-info.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'settings')">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
                <i class="fas fa-chevron-{{ request()->routeIs('company-info.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-settings"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('company-info.*') ? 'show' : '' }}" id="submenu-settings">
                <a href="{{ route('company-info.edit') }}" class="sidebar-submenu-item {{ request()->routeIs('company-info.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>Company Info</span>
                </a>
            </div>
        </div>
        @endcan

        {{-- 12. Access Control (admin, last) --}}
        @canany(['view users', 'view roles'])
        <div class="sidebar-menu-group {{ request()->routeIs('user-management.*') || request()->routeIs('role-management.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'access-control')">
                <i class="fas fa-shield-alt"></i>
                <span>Access Control</span>
                <i class="fas fa-chevron-{{ request()->routeIs('user-management.*') || request()->routeIs('role-management.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-access-control"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('user-management.*') || request()->routeIs('role-management.*') ? 'show' : '' }}" id="submenu-access-control">
                @can('view users')
                <a href="{{ route('user-management.index') }}" class="sidebar-submenu-item {{ request()->routeIs('user-management.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Users</span>
                </a>
                @endcan
                @can('view roles')
                <a href="{{ route('role-management.index') }}" class="sidebar-submenu-item {{ request()->routeIs('role-management.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag"></i>
                    <span>Roles</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany
    </nav>
</div>
